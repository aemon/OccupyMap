<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700' rel='stylesheet' type='text/css'>
	<script type="text/javascript">
  //define some global js constants for this page
	 var site_url = "<?=url::site()?>";
	</script>

	<?php echo $header_block; ?>
		<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
	<script type="text/javascript" src="/themes/occupy/js/jquery.timepicker.js"></script>
	<script type="text/javascript" src="/themes/occupy/js/jquery.soulmate.js"></script>

</head>


<?php 
  // Add a class to the body tag according to the page URI
if (isset($uri_segments))
{
  // we're on the home page
  if (count($uri_segments) == 0) 
  {
    $body_class = "page-main";
  }
  // 1st tier pages
  elseif (count($uri_segments) == 1) 
  {
    $body_class = "page-".$uri_segments[0];
  }
  // 2nd tier pages... ie "/reports/submit"
  elseif (count($uri_segments) >= 2) 
  {
    $body_class = "page-".$uri_segments[0]."-".$uri_segments[1];
  };
    
  echo '<body id="page" class="'.$body_class.'" />';
	
} else {

	echo '<body id="page">';

}
?>
<script type="text/javascript" src="https://nav.occupy.net/occupynet_nav.js"></script>
  <!-- top bar -->
  <div id="top-bar">
		<h1><span style="color:#fff">#Occupy</span>Map</h1>
		
    <!-- searchbox -->
		<div id="searchbox">
			<!-- languages -->
			<?php echo $languages;?>
			<!-- / languages -->
			<!-- searchform -->
			<?php //echo $search; ?>
			<div class="search-form">
  			<form id="search" action="<?=url::site()?>search" method="get">
  		  	<ul>
            <li>
              <input id='search-input' type='text' name='q' value='Search a city, address, #hashtag or keyword' autocomplete='off'/>      
            </li>
          </ul>
        </form>
			</div>
			
			<!-- / searchform -->
      <script type="text/javascript">
      $(document).ready(function(){
            (function() {
              var render, select;
              $('#search-input').focus();
              render = function(term, data, type, index) {
                var ret = {
                  city:function(){
                    //city name, that's it (remove integers)
                    return data.title
                  },
                  tag: function(){
                    //highlight term in incident name
                    return data.title
                  },
                  location: function(){
                    //title location name
                    //subtitle: incident count
                    return data.title + "<span class=\"subtitle\">" 
                    + data.incident_count + " reports</span>"
                  },
                  incident: function(){
                    //title: incident name
                    //subtitle: date, address
                    //hasVideo, hasNews, hasPhoto
                    return data.title + "<span class=\"subtitle\">" 
                    + data.category_title + " at "
                    + data.location
                    + "</span>";
//                    var re = new RegExp(index, "g");
//                    return out.replace(re,"<span class=\"search-term\">"+term+"</span>");
                    
                  }
                }
                var out = ret[type];
                
                return out();
//                return term;
              };
              select = function(term, data, type) {
                window.location.replace(site_url + data.url);

              };
              
              var location_select = function(term, data, type){
                $("#location_find").val(term)
                $("#location_name").val(term);
                geoCode();
                //stupid wait for asynchronous geocode return
                //can we catch the map change in this scope?
                window.setTimeout(function(){$("#location_name").val(term);
                $("#soulmate").fadeOut('slow');
                },1500);
              }

              $('#search-input').soulmate({
                url: 'http://map.occupy.net:5656/search',
                types: ['incident', 'location','city','tag'],
                renderCallback: render,
                selectCallback: select,
                minQueryLength: 3,
                maxResults: 5
              });
              
              $('#location_find').soulmate({
                url: 'http://map.occupy.net:5656/search',
                types: ['location'],
                renderCallback: render,
                selectCallback: location_select,
                minQueryLength: 3,
                maxResults: 10
              });
              
              
        $('#search-input').blur();
        $('#search-input').focus(function(){
          var pos = $("#search-input").offset();
          
          $("#soulmate").css("position","absolute");
          $("#soulmate").css("top",pos.top +"px")
          $("#soulmate").css("right","120px")
          $(this).val('')});
        $('#location_find').blur();
        $('#location_find').focus(function(){
          var pos = $("#location_find").offset();
          $("#soulmate").css("position","absolute");
          $("#soulmate").css("top",pos.top +"px")
          $("#soulmate").css("right","450px")

         // $("#soulmate").css("left",pos.left +"px")

          $(this).val('')
          
        });
        
        }).call(this);
        
        
        //youtube prepopulate on submit forms
         $("#check_youtube").change(function(){
				   //do the youtube query, get json and prepopulate
          v_url = $("#check_youtube").val();
          $("#incident_title").css("background-color","#ffc");
          $("#incident_description").css("background-color","#ffc");
          $("#incident_title").val("Loading...");
          $("#incident_description").val("Loading...");


          if ((v_url.split("youtube.com").length >1 )|| (v_url.split("vimeo.com").length >1)) {
					   $.ajax({
					     url:"http://map.occupy.net:9494/video/"+v_url,
					     dataType:'json',
					     success:function(data) {
					       $("#incident_title").val(data.title);
					       $("#incident_description").val(data.url +" \n \n"+ data.description);
					       $("input.video").first().val(v_url);
                 $("#incident_title").css("background-color","#fff");
                 $("#incident_description").css("background-color","#fff");

					       
                //set the date also.  would be great to prompt a location
					     }
					   })
          }
				   
				 })
				
        $("#filter-menu-toggle").click(function(){
          $("#the-filters").toggle(400);
        })
        
        $("#layers-menu-toggle").click(function(){
          $("#kml_switch").toggle(400);
        })
        
        $("#activity-menu-toggle").click(function(){
          $("#activity-menu").toggle(400);
          if ($("#activity-menu-toggle").hasClass("ic-down") ){
            $("#activity-menu-toggle").removeClass("ic-down");
          } else {
            $("#activity-menu-toggle").addClass("ic-down");
          }
          
        })
        
        //javascript to parse query string
        //http://stackoverflow.com/questions/901115/get-query-string-values-in-javascript
        
        
        
        var urlParams = {};
        (function () {
            var e,
                a = /\+/g,  // Regex for replacing addition symbol with a space
                r = /([^&=]+)=?([^&]*)/g,
                d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
                q = window.location.search.substring(1);

            while (e = r.exec(q))
               urlParams[d(e[1])] = d(e[2]);
        })();
        //we have a time interval - manipulate the timeline
        if (urlParams["startTime"] > 0 && urlParams["endTime"] > 0) {
          day = parseInt(urlParams["startTime"]);
          nextDay = parseInt(urlParams["endTime"]);
          stop =false;
          $("#startDate").find("option").each(function(i,e){
            var v = ($(e).attr("value"));
            if (v>=day && stop==false){
              stop = true;
              $("#startDate").trigger('click');
              $("#startDate").val(v);
              $(e).select()
            }
          })
          stop=false;
          $("#endDate").find("option").each(function(i,e){
            var v = ($(e).attr("value"));
            if (v>=nextDay && stop==false){
              $("#endDate").trigger('click');
              $("#endDate").val(v);
              $(e).select();
              stop = true;
            }
          })        
          $("#startDate").trigger('change');
          $("#endDate").trigger('change');
        }        
        
        if (urlParams["lat"] && urlParams["lon"] && urlParams["zoom"]){
      		// Create a lat/lon object and center the map
      		var myPoint = new OpenLayers.LonLat(parseFloat(urlParams["lon"]), parseFloat(urlParams["lat"]));
        	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
        	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
      		myPoint.transform(proj_4326, proj_900913);

      		// Display the map centered on a latitude and longitude
      		map.setCenter(myPoint, parseInt(urlParams["zoom"]));
          
        }

        
      })
      </script>			
			<!-- user actions -->
			<div id="loggedin_user_action" class="clearingfix">
				<?php if($loggedin_username != FALSE){ ?>
					<a href="<?php echo url::site().$loggedin_role;?>"><?php echo $loggedin_username; ?></a> <a href="<?php echo url::site();?>logout/front"><?php echo Kohana::lang('ui_admin.logout');?></a>
				<?php } else { ?>
					<a href="<?php echo url::site()."members/";?>"><?php echo Kohana::lang('ui_main.login'); ?></a>
				<?php } ?>
			</div>
			<!-- / user actions -->
    </div>
		<!-- / searchbox -->
  </div>
	<!-- / top bar -->

	<!-- mainmenu -->
	<div id="mainmenu" class="clearingfix">
		<ul>
			<?php nav::main_tabs($this_page); ?>
		</ul>

	</div>
	<!-- / mainmenu -->

	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<!-- header -->
		<div id="header">
			
			<!-- logo -->
			<?php if($banner == NULL){ ?>
			<div id="logo">
				<h1><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></h1>
				<span><?php echo $site_tagline; ?></span>
			</div>
			<?php }else{ ?>
			<a href="<?php echo url::site();?>"><img src="<?php echo url::base().Kohana::config('upload.relative_directory')."/".$banner; ?>" alt="<?php echo $site_name; ?>" /></a>
			<?php } ?>
			<!-- / logo -->
			
			<!-- submit incident -->
			<?php echo $submit_btn; ?>
			<!-- / submit incident -->
			
		</div>
		<!-- / header -->

		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">

