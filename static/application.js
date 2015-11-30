$(document).ready(function(){
	// Toggle Elements
	$(".this-toggle").click(function(e){
		e.preventDefault();
		var el = $(this);
		// Hide target
		if(el.data("hide")){
			$(el.data("hide")).hide();
		}		
		// Toggle Active
		$("body").find(".this-toggle").removeClass("active");
		$(el.data("target")).slideToggle("medium",function(){
			if($(el.data("target")).is(":visible")){
				el.addClass("active");
			}			
		});
	});	
  // Filter
  $('select.filter').chosen().change(function(e,v){
      var href=document.URL.split("?")[0].split("#")[0];
      window.location=href+"?"+$(this).attr("data-key")+"="+v.selected;
  });    
  /**
   * Custom Radio Box
   */
  $(document).on('click','.form_opt li a',function(e) {
    var href=$(this).attr('href');
    var name = $(this).parent("li").parent("ul").attr("data-id");
    var to = $(this).attr("data-value");
    var callback=$(this).parent("li").parent("ul").attr("data-callback");
    if(href=="#" || href=="") e.preventDefault();

    $("input#" + name).val(to);
    $(this).parent("li").parent("ul").find("a").removeClass("current");
    $(this).addClass("current");
    if(callback !==undefined){
      window[callback](to);
    }      
  });   
	// Select Elements
	$(".this-select").click(function(){
		$(this).select();
	});
 	// Predefined size > change in embed code
  $("#predefined-size").change(function(){
  	var a = $(this).val().split("x");
    var b = $('#embed textarea').val().replace(/width='(.*?)' height='(.*?)'/,"width='"+a[0]+"' height='"+a[1]+"'");
    $('#embed textarea').val(b).select(); 	
  });
  $("#show-language").click(function(e){
    e.preventDefault();
    $(".langs").fadeToggle();
  });
  // Active Menu
  var path = location.pathname.substring(1);
  if (path) {
    $('#main-menu ul.nav li a').removeClass("active");
    $('#main-menu ul.nav li a[href$="' + path + '"]').addClass('active');
    $('.profile-menu-list li a').removeClass("current");
    $('.profile-menu-list li a[href$="' + path + '"]').addClass('current');
  }
  // Update Notify
  $.notify.defaults( {
    globalPosition: 'bottom right'
  });
  // Easy Popups
  $(document).on('click',"a.u_share,.popup",function(e){
    e.preventDefault();
    window.open($(this).attr("href"), '', 'left=50%, top=100, width=700, height=600, personalbar=0, toolbar=0, scrollbars=1, resizable=1')    
  });    
    //Easy Tabs
    $(".tabbed").hide();
    $(".tabbed").filter(":first").fadeIn();
    $(".tabs a").click(function(e){
      e.preventDefault();;
      $(".tabs li, .tabs a").removeClass("active");
      $(this).parent("li").addClass("active");
      $(this).addClass("active");
      $(".tabbed").hide();
      $($(this).attr("href")).fadeIn();
    }); 
  // Chosen
  $("select").not("#predefined-size").chosen({disable_search_threshold: 5});
  // Dependant Categories  
  // Dependant Categories
  if($("#category").length > 0){
    var video_categories = $("#category").html();
    if(!is_mobile()){
      $(".type").chosen().change(function(e,v){
        if(v.selected == "video"){
          var html = video_categories;
        }else{
          var html = $("."+v.selected+"").html();
        }
        $("#category").html(html).trigger('chosen:updated');      
      });
    }else{
    $(".type").change(function(){
      var v = $(this).find("option:selected").val();
      if(v == "video"){
        var html = video_categories;
      }else{
        var html = $("."+v+"").html();
      }
      $("#category").html(html);      
    }); 
    }    
  }     
  // Dependant Categories
  if($("#category-1").length > 0){
    var video_categories = $("#category-1").html();
    if(!is_mobile()){
      $(".type-1").chosen().change(function(e,v){
        if(v.selected == "video"){
          var html = video_categories;
        }else{
          var html = $("."+v.selected+"-1").html();
        }
        $("#category-1").html(html).trigger('chosen:updated');      
      });
    }else{
      $(".type-1").change(function(){
        var v = $(".type-1 option:selected").val();
        if(v == "video"){
          var html = video_categories;
        }else{
          var html = $("."+v+"-1").html();
        }
        $("#category-1").html(html);      
      }); 
    }    
  }     
  /**
   * Show forgot password form
   **/
   $(document).on('click','#forgot-password',function(){
      show_forgot_password();
   });
   if(location.hash=="#forgot"){
      show_forgot_password();
   }  
  // Hide Alert
  $(document).on('click',"div.alert",function(){
    $(this).fadeOut();
  });
  // Tooltip
  $(".this-tooltip").tooltip();
  /**
   * Update Themes
   **/
  $(".cover-selector li a").click(function(e){
    e.preventDefault();
    var c=$(this).attr("data-value");
    $(".cover-selector li a").removeClass("current");
    $(this).addClass("current");   
    $("#cover_value").val(c);
  });    
  /**
   * Toggle Description
   */
  if($(".this-description").height() < 150) $(".toggle-description").hide();

  $(".toggle-description").click(function(e){
    e.preventDefault();
    $(".this-description").toggleClass("truncate");
  });
  var max = -1;
  $("ul.media > li").not("ul.media-sidebar-featured li").each(function() {
      var h = $(this).height(); 
      max = h > max ? h : max;
  });
  $("ul.media > li").not("ul.media-sidebar-featured li").css("min-height", max);  

  // Play gif
  $(document).on('click',".play-gif",function(e){
    e.preventDefault();
    console.log($(this).data("play"));
    if($(this).attr("data-play") == "1"){
      $(this).find("img").attr("src", $(this).data("static"));
      $(this).attr("data-play", "0");
      $(this).find("span").show();
    }else{
      $(this).find("img").attr("src", $(this).data("gif"));
      $(this).attr("data-play", "1");
      $(this).find("span").hide();
    }
  });
  $('.scroll').infinitescroll({
   loading: {
      finished: undefined,
      finishedMsg: "",
      img: appurl+"/static/pre.gif",
      msg: null,
      msgText: "<em>Loading...</em>",
      selector: null,
      speed: 'fast',
      start: undefined
    },
    navSelector   : ".ajax_load",
    nextSelector  : ".ajax_load",
    itemSelector  : ".scroll",
    dataType    : 'html',
    path: function(index) {
      return "?page=" + index;
    }    
    });
  $(".sticky").sticky();
  $(".modal-trigger,.delete").click(function(e){
    e.preventDefault();
    if(!$(this).hasClass("doajax")) $(this).modal();
    return false;
  });  
});
/**
 * Login Modal
 */
function login_modal(){
  var html = $(".login-form").html();
  $(document).modal({title: "Login to your account", content: html, link: ""});
}
/**
 * Show Password Field Function
 **/
function show_forgot_password(){
  $("#login_form").slideUp("slow");
  $("#forgot_form").slideDown("slow");  
  return false  
}