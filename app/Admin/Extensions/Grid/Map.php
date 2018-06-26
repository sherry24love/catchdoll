<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/20
 * Time: 23:30
 */

namespace App\Admin\Extensions\Grid ;



use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Map extends AbstractDisplayer {

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Type of editable.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Options of editable function.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Add options for editable.
     *
     * @param array $options
     */
    public function addOptions($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public static function getAssets()
    {

        $js = [
            '//api.map.baidu.com/api?v=2.0&ak=BbD2XGK1teOG32jihZD5955r' ,
            asset('vendor/layer/layer.js')
        ];

        return $js ;
    }

    public function display( )
    {
        // TODO: Implement display() method.
        Admin::js( self::getAssets() );
        Admin::css([
            asset('vendor/layer/skin/default/layer.css')
        ]);
        $lat = data_get ( $this->row , 'lat' , 0 );
        $lon = data_get( $this->row , 'lon' , 0 );


        Admin::script( $this->script() ) ;
        return <<<EOT
        <button type="button"
            class="btn btn-secondary btn-map"
            title="popover"
            data-container="body"
            data-key="{$this->getKey()}" 
            data-lat="{$lat}" 
            data-lon="{$lon}" 
            data-placement="wtf"
            data-content="{$this->value}"
            >
          {$this->value}
        </button>
EOT;
    }


    protected function script()
    {
        $name = $this->column->getName();

        return <<<EOT
$('.btn-map').on('click' , function(){
    var btn = $(this);
    layer.open({
        type : 1 ,
        content : "<div id='map' style='width:100%;height:100%;'>hello world</div>" ,
        area: ['800px', '500px'],
        success: function(layero, index){
            console.log(layero, index);
            var lat = $( btn ).data('lat');
            var lng = $( btn ).data('lon');
            
			
		    //var ggPoint = new BMap.Point(lat,lng);
		
		    //地图初始化
		    var bm = new BMap.Map("map");
		    bm.addControl(new BMap.NavigationControl());
            function showInfo(e){
                var geoc = new BMap.Geocoder();
                
                geoc.getLocation( e.point , function(rs){
                    var addComp = rs.addressComponents;
                    var address = addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber ;
                    btn.text( address )
                    console.log( e.point )
                    $.ajax({
                        url: "{$this->getResource()}/" + $(btn).data('key'),
                        type: "POST",
                        data: {
                            $name: address  ,
                            lat : e.point.lat , 
                            lon : e.point.lng ,
                            _token: LA.token,
                            _method: 'PUT'
                        },
                        success: function (data) {
                            toastr.success(data.message);
                        }
                    });
                });  
                layer.close( index )
			}
			bm.addEventListener("click", showInfo);

            if( lat =='' || lat == 0 || lng =='' || lng == 0 ) {
            	bm.centerAndZoom( '深圳' , 15);
            } else {
            	var ggPoint = new BMap.Point(lng , lat  );
            	bm.centerAndZoom( ggPoint , 15);
            	var marker = new BMap.Marker( ggPoint );
				bm.addOverlay(marker);
    		}
        }
        
    }) ;
});
EOT;
    }
}