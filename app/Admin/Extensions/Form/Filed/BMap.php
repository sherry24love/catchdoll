<?php

namespace App\Admin\Extensions\Form\Filed ;

use Encore\Admin\Form\Field;

class BMap extends Field
{
    /**
     * Column name.
     *
     * @var array
     */
    protected $column = [];
    
    protected $view = "admin.extensions.form.bmap";

    /**
     * Get assets required by this field.
     *
     * @return array
     */
    public static function getAssets()
    {
        
        $js = '//api.map.baidu.com/api?v=2.0&ak=BbD2XGK1teOG32jihZD5955r';

        return compact('js');
    }

    public function __construct($column, $arguments)
    {
        $this->column['lat'] = $column;
        $this->column['lng'] = $arguments[0];

        array_shift($arguments);

        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);

        $this->useTencentMap();
        
    }

    public function useTencentMap()
    {
        $this->script = <<<EOT
        function initTencentMap(name) {
            var lat = $('#{$this->id['lat']}');
            var lng = $('#{$this->id['lng']}');
            var address = $('#address');
            if( address.length > 0 ) {
            	var geoc = new BMap.Geocoder();  
    		}
			
		    //var ggPoint = new BMap.Point(lat,lng);
		
		    //地图初始化
		    var bm = new BMap.Map("map_"+name);
		    bm.addControl(new BMap.NavigationControl());
            function showInfo(e){
				lat.val( e.point.lat );
                lng.val( e.point.lng );
                if( address.length && geoc ) {
	                geoc.getLocation( e.point , function(rs){
						var addComp = rs.addressComponents;
						address.val(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
					});  
                }
                bm.clearOverlays();   
				var marker = new BMap.Marker(new BMap.Point( e.point.lng, e.point.lat ));
				bm.addOverlay(marker);
			}
			bm.addEventListener("click", showInfo);

            if( lat.val() =='' || lat.val() == 0 || lng.val() =='' || lng.val() == 0 ) {
            	bm.centerAndZoom( '深圳' , 15);
            } else {
            	var ggPoint = new BMap.Point(lng.val() , lat.val()  );
            	bm.centerAndZoom( ggPoint , 15);
            	var marker = new BMap.Marker( ggPoint );
				bm.addOverlay(marker);
    		}
    		
    		$('.map-ok').bind('click' , function(){
    			var ggPoint = new BMap.Point(lng.val() , lat.val()  );
            	bm.centerAndZoom( ggPoint , 15);
            	bm.clearOverlays();  
            	var marker = new BMap.Marker( ggPoint );
				bm.addOverlay(marker);
    		});
        }

        initTencentMap('{$this->id['lat']}{$this->id['lng']}');
EOT;
    }
}
