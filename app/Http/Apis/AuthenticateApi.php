<?php
namespace App\Http\Apis ;


use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Socialite;

use EasyWeChat ;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;
use Cache ;
use Hashids\Hashids ;


use Apiato\Core\Abstracts\Controllers\ApiController;
use App\Models\UserRsync;
use function EasyWeChat\Kernel\Support\str_random;

class AuthenticateApi extends ApiController {

	use AuthenticatesUsers ;
	
	//
	public function __construct() {
		
		$this->middleware('auth:api')->only([
				'logout' , 'updatebase' , 'user'
		]);
	}
	
	public function user() {
		$user = auth()->guard('api')->user();
		return response()->json([
		    'errcode' => 0 ,
            'user' => $user
        ]) ;
	}

	public function setMobile( Request $request ) {
	    $user = auth()->guard('api')->user();
        $miniProgram = EasyWeChat::miniProgram(); // 小程序

        $session = Cache::get( $user->id );
        $iv = $request->input('iv');
        $encryptData = $request->input('encrypt_data');
        $decryptedData = $miniProgram->encryptor->decryptData( data_get( $session , 'session_key' ), $iv, $encryptData);
        $mobile = data_get( $decryptedData , 'phoneNumber' );
        if( !$mobile ) {
            return response()->json([
                'errcode' => 10001 ,
                'msg' => '手机号码为空'
            ]);
        }
        if( User::where('id' , $user->id )->update( ['mobile' => $mobile ] ) ) {
            return response()->json([
                'errcode' => 0 ,
                'data' => $mobile ,
                'msg' => '手机号码绑定成功'
            ]);
        }
        return response()->json([
            'errcode' => 10001 ,
            'msg' => '手机号码绑定失败'
        ]);
    }
	
	
	public function session( Request $request ) {
		$code = request()->input('code');
		$miniProgram = EasyWeChat::miniProgram(); // 小程序
		$session = $miniProgram->auth->session( $code ) ;
		//获取到session
		//TODO 获取openid 对应的用户
		$user = UserRsync::with('user')->where('type' , 'mini' )->where('token' , data_get( $session , 'openid' ))->first();
		
		//如果这里找到了 则直接返回登录信息
		if( empty( $user ) ) {
			\DB::beginTransaction();
			try {
				$count = User::withTrashed()->count();
				$hashids = new Hashids( env('APP_NAME') , 10);
				$id = $hashids->encode( $count + 1 ) ;
				$user = User::create([
						'username' => $id ,
						'level' => 0 ,
						'password' => bcrypt( str_random( 8 ) )
				]);
				if( $user ) {
					$rsync = new UserRsync([
							'type' => 'mini' ,
							'token' => data_get( $session , 'openid' ) ,
					]);
					$user->rsync()->save( $rsync );
				}
				\DB::commit();
			} catch ( \Exception $e ) {
				\Log::info( $e->getTraceAsString() );
				\DB::rollBack();
			}
		} else {
			$user = $user->user ;
		}
        $expiresAt = Carbon::now()->addMinutes(600);
        Cache::put( $user->id , $session , $expiresAt ) ;
		// 直接创建token
		$password_client = Client::query()->where('password_client',1)->latest()->first();
		
		$request->request->add([
				'grant_type' => 'password',
				'client_id' => $password_client->id,
				'client_secret' => $password_client->secret,
				'username' =>  $user->username ,
				'password' => $user->password ,
				'scope' => ''
		]);
		
		$proxy = Request::create(
				'oauth/token',
				'POST'
		);
		
		$response = \Route::dispatch($proxy);
		
		return $response;
		//$token = $user->createToken( data_get( $session , 'openid' ) )->accessToken;
		//return $user ;
	}
	
	public function username()
	{
		return 'phone';
	}
	
	// 登录
	public function login(Request $request)
	{
		/**
		$validator = Validator::make($request->all(), [
				'phone'    => 'required|exists:user',
				'password' => 'required|between:5,32',
		]);
	
		if ($validator->fails()) {
			$request->request->add([
					'errors' => $validator->errors()->toArray(),
					'code' => 401,
			]);
			return $this->sendFailedLoginResponse($request);
		}
	
		$credentials = $this->credentials($request);
		**/
		$this->guard('api')->loginUsingId( 1 );
		return $this->sendLoginResponse($request);
		if ($this->guard('api')->attempt($credentials, $request->has('remember'))) {
			return $this->sendLoginResponse($request);
		}
	
		return $this->setStatusCode(401)->failed('登录失败');
	}
	
	// 退出登录
	public function logout(Request $request)
	{
	
		if (Auth::guard('api')->check()){
	
			Auth::guard('api')->user()->token()->revoke();
	
		}
	
		return $this->json('退出登录成功');
	
	}


    /**
     * @param Request $request
     * @return mixed
     * 更新用户的基本信息
     */
    public function updatebase( Request $request ) {
        $user = auth()->guard('api')->user();
        $data = [] ;
        $data['nickname' ] = $request->input('nickname' );
        $data['avatar'] = $request->input('avatar');
        $data['gender'] = $request->input('gender'  , 1 );
        if( User::where('id' , $user->id )->update( $data ) ) {
            return response()->json([
                'errcode' => 0 ,
                'msg' => '更新成功'
            ]);
        }
        return response()->json([
            'errcode' => 10001 ,
            'msg' => '更新失败'
        ]);
    }
	
	//调用认证接口获取授权码
	protected function authenticateClient(Request $request)
	{
		$credentials = $this->credentials($request);
	
		// 个人感觉通过.env配置太复杂，直接从数据库查更方便
		$password_client = Client::query()->where('password_client',1)->latest()->first();
		
		$request->request->add([
				'grant_type' => 'password',
				'client_id' => $password_client->id,
				'client_secret' => $password_client->secret,
				'username' => $credentials['phone'],
				'password' => $credentials['password'],
				'scope' => ''
		]);
	
		$proxy = Request::create(
				'oauth/token',
				'POST'
		);
	
		$response = \Route::dispatch($proxy);
	
		return $response;
	}
	
	protected function authenticated(Request $request)
	{
		return $this->authenticateClient($request);
	}
	
	protected function sendLoginResponse(Request $request)
	{
		$this->clearLoginAttempts($request);
	
		return $this->authenticated($request);
	}
	
	protected function sendFailedLoginResponse(Request $request)
	{
		$msg = $request['errors'];
		$code = $request['code'];
		return $this->json( $msg , $code );
	}
}