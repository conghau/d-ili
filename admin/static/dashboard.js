/**
 * 
 **/
 $(document).ready(function(){
  /**
   * Easy Tabs
   **/
    $(".tabbed").hide();
    $(".tabbed").filter(":first").fadeIn();
    $(".tabs a").click(function(e){
      if($(this).attr("data-link")){
        return;
      }
      e.preventDefault();
      var id=$(this).attr("href");
      $(".tabs li").removeClass("active");
      $(this).parent("li").addClass("active");
      $(".tabbed").hide();
      $(id).fadeIn();
      if(!is_mobile() && !is_tablet()){
        update_sidebar();
        $(".sub-sidebar").height($(id).height()+100);
      }
    }); 
    var path = location.pathname.substring(1);
    if (path) $('ul.sidenav li a[href$="' + path + '"]').parent("li").attr('class', 'current');
    if (path) $('ul.sidenav li a[href$="' + path + '"]').attr('class', 'current');    
    
    $(".sidebar ul.sidenav li,.sidebar ul.sidenav li div a").each(function(){
        if($(this).hasClass("current")){
           $(this).parent("div").slideDown("slow",function(){
               $(this).parent("li").addClass("current");
            });         
            $(this).find("div").slideDown("slow");
        }
    });                 
    $(".sidebar ul.sidenav li > a").click(function(){
        if($(this).hasClass("current")){
            return false;
        }
        var link= $(this).attr("href");
        if(link ==""){
         $(".sidebar ul.sidenav li").removeClass("current");
         $(".sidebar ul.sidenav li").find("div").slideUp("slow");
         $(this).parent("li").find('div').slideToggle("slow");
         $(this).parent("li").addClass('current');
          return false;
        }     
          
    });       		
  // Chosen
  $("select:not(.notchosen)").chosen({disable_search_threshold: 5});
  // Dependant Categories
  if($("#category").length > 0){
    var video_categories = $("#category").html();
    $("#type").chosen().change(function(e,v){
      if(v.selected == $("#category").data("active")){
        var html = video_categories;
      }else{
        var html = $("."+v.selected).html();
      }
      $("#category").html(html).trigger('chosen:updated');
    });    
  }
  /**
   * Update Filter + Theme
   **/
  $('select#filter').chosen().change(function(e,v){
      var href=document.URL.split("?")[0].split("#")[0];
      window.location=href+"?filter="+v.selected;
  });  
  $('select#theme_files').chosen().change(function(e,v){
      window.location=appurl+"/editor/"+$("select#theme_files").val();
  });  
  /**
   * Update Themes
   **/
  $(".themes-style li a").click(function(e){
    e.preventDefault();
    var c=$(this).attr("data-class");
    $(".themes-style li a").removeClass("current");
    $(this).addClass("current");   
    $("#theme_value").val(c);
  });    
  /**
   * Delete Alert
   **/
  $(".modal-trigger,.delete").click(function(e){
    e.preventDefault();
    if(!$(this).hasClass("doajax")) $(this).modal();
    return false;
  }); 
  $(".toggle").click(function(e){
    e.preventDefault();
    var target = $($(this).attr("data-target"));
    target.fadeToggle();
  });
  // Remove logo
  $("#remove_logo").click(function(e){
    e.preventDefault();
    $("#setting-form").append("<input type='hidden' name='remove_logo' value='1'>");
    $(this).text("Logo will be removed upon submission");
  });  
  // Remove Alert
 $("div.alert").click(function(){
    $(this).fadeOut();
 });   
 //Back to top
  $(window).scroll(function(){   
    if(window.pageYOffset>300){
      $("#back-to-top").fadeIn('slow');
    }else{
      $("#back-to-top").fadeOut('slow');
    }
  });
  $("a#back-to-top, a.scroll").smoothscroll();  

  // Check All
  $('#check-all-btn').on('click', function(e) {
    e.preventDefault();
    if($("body").find('.data-delete-check').prop('checked')){
      $(this).text("Select All");
      $(this).prop('checked', false);
      $("body").find('.data-delete-check').prop('checked', false);
    }else{
      $(this).text("Unselect All");
      $(this).prop('checked', true);
      $("body").find('.data-delete-check').prop('checked', true);
    }    
  });  
  $('#check-all').on('click', function(e) {
    e.preventDefault();
    var form=$(this).parents("form");
    $("p.cta-hide").fadeIn();
    if(form.find('.data-delete-check').prop('checked')){
      $(this).text("Select All");
      $(this).prop('checked', false);
      form.find('.data-delete-check').prop('checked', false);
    }else{
      $(this).text("Unselect All");
      $(this).prop('checked', true);
      form.find('.data-delete-check').prop('checked', true);
    }    
  });     
  $("#delete-all").click(function(e){
    e.preventDefault();
    $("#delete-selected-media").submit();
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
  // Charts
  if($(".chart").length > 0){
    function showTooltip(x, y, c) {
      $('<div id="tooltip" class="chart-tip">' + c + '</div>').css( {
          position: 'absolute',
          display: 'none',
          top: y - 40,
          left: x - 30,
          color: '#fff',
          opacity: 0.80
      }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    var previousSeries = null;
    $(".chart").bind("plothover", function (event, pos, item) {
      if(item){
        if(previousSeries != item.seriesIndex || previousPoint != item.dataIndex){
          previousPoint = item.dataIndex;
          previousSeries = item.seriesIndex; 
          $("#tooltip").remove();
          showTooltip(item.pageX, item.pageY, item.datapoint[1]+" "+item.series["label"]);          
          $("#tooltip").addClass(item.series["label"].toLowerCase());             
        }                      
      }
    });     
  }
  /**
   * Progress bar
   */
    $(".progress").each(function() {
        var percent = parseInt($(this).find(".progress-bar").data("now"));
        if (percent >= 100) {
            percent = 100;
        }
        $(this).find(".progress-bar").animate({
            width: percent+"%"
        }, 1500);
    });  
  /**
   * Import from Youtube
   **/
  $("a.import-this").click(function(e){
    import_this($(this));
  });
  $("#import_videos").click(function(e){
    var val = [];
    $(".this-import").each(function(){
      if($(this).filter(':checkbox:checked').length > 0){
        val.push($(this).val());
      }
    });
    var arrayLength = val.length;
    for (var i = 0; i < arrayLength; i++) {
      import_this($("#button-"+val[i]));
    }
  });
  function import_this(it){
    var n = it.attr("data-n");
    var id = it.attr("data-id");
    $.ajax({ 
        type: "POST",
        url: appurl+"/server/import",
        dataType:"json",
        data: "token="+token+"&id=" + id + "&type=" + it.attr("data-type") +"&cat="+ $("#yt-" + n + " .option").val() +"&feat="+$("#yt-" + n + " .feature").val()+"&url="+$("#yt-" + n + " .medialink").attr("href")+"&import=true",
        success: function(a) {
            if (a.error) {
              $("#yt-" + n + "").addClass("danger");
              $("#yt-" + n + " #import-data").html(a.msg);
            } else {
              $("#yt-" + n + "").addClass("success");
              $("#yt-" + n + " #import-data").html(a.msg);
            }
        }
    });
  }
  /**
   * Ajax Search
   */
  $("#ajax_media_search").submit(function(e){
      e.preventDefault();
      var q = $(this).find("input#q");

      $(this).find(".form-group").removeClass("has-error");
      $(this).find(".help-block").remove();

      if(q.val().length < 3) {
        $(this).find(".form-group").addClass("has-error");
        q.after("<p class='help-block'>Keyword must be at least 4 characeters</p>");
        return false;
      }
      $.ajax({ 
          type: "POST",
          url: appurl+"/server/search",
          data: "token="+token+"&q=" + q.val() + "&type=" + $(this).attr("data-type") +"&ajax=true",
          beforeSend: function() {
            $("#media-holder").html("<img class='loader' src='"+appurl+"/static/images/loader.gif' style='margin-left:15px;border:0;' />")
          },
          complete: function() {
            $("#media-holder").find('.loader').fadeOut("slow");
            $(".media-sort").slideUp();
          },
          success: function(a) {
            $("#media-holder").html(a);
          }
      });
    });  
  // Add page > Permalink Generator
  if($("#slug").length > 0 && $("#slug").val().length == 0){
    $("#title").keyup(function() {
        if($(this).attr("data-ignore")) return;
        var seo = $(this).val().trim();
        seo = seo.toLowerCase();
        seo = seo.replace(/[^a-zA-Z0-9]+/g, '-');
        if(seo.length < 3) return false;
        if(seo.length > 60) seo = seo.substring(0,60);      
        $("#slug").val(seo);
        $("#slug").parent(".hide-callback").show("slow");
        return false;
    });    
  }  
  $("#slug").keyup(function() {
    var l = $(this).val().toLowerCase().replace(/[^a-zA-Z0-9]+/g, '-');
    if(l.length >= 60) {
      $(this).parent("div").find(".label").removeClass("label-success").addClass("label-danger");
      $(this).val(l.substring(0,60));
    }else{
      $(this).parent("div").find(".label").removeClass("label-danger").addClass("label-success");      
      $(this).val(l);
    }
    $(this).parent("div").find(".label").text(l.length + " characeters (max 60)");
  });
  // JS Libraries
  $('#tags').tagsInput({'width':'100%', 'height': '64px','minChars' : 3});
  // Menu Editor
  $("#add_to_menu").submit(function(e){
    e.preventDefault();
    var text = $(this).find("#title").val();
    var href = $(this).find("#url").val();
    var icon = $(this).find("#fa").val();
    var html = '<li><div class="input-group"><span class="input-group-addon"><i class="fa fa-'+icon+'"></i></span><a href="#'+href+'">'+text+'<span class="menu-delete btn btn-danger btn-xs pull-right">Delete</span></a><input type="hidden" name="menu[]" value=\'{"href":"'+href+'","text":"'+text+'","icon":"'+icon+'"}\'></div></li>';
      $("#sortable").append(html);
  });   
  $(document).on("click",".menu-delete", function(e){
    e.preventDefault();
    $(this).parents("li").remove();
  });
  $("#save_menu").click(function(e){
    e.preventDefault();
     $.ajax({ 
          type: "POST",
          url: appurl+"/server/menu/add",
          data: $("#current_menu").serialize(),
          success: function(a) {            
            $("#save_menu").addClass("btn-success").text("Saved");
          }
      });
  });
  $(".add_custom").submit(function(e){
    e.preventDefault();
    var d = $(this).serializeArray();    
    d = d[0]["value"].split("||");
    var text = d[0];
    var href = d[1];
    $("#add_to_menu #title").val(text);
    $("#add_to_menu #url").val(href);
  });
  $(".doajax").click(function(e){
    e.preventDefault();
    var t = $(this);
    $.get($(this).attr("href"));    
    $(this).parents("li").fadeOut('slow',function(){
      $(this).remove();
      if(t.data("media") == "delete"){
        $("#media-holder h4").after("<div class='alert alert-danger'>Media has been deleted.</div>");
      }else{
        $("#media-holder h4").after("<div class='alert alert-success'>Media has been approved.</div>");
      }
      setTimeout(function() {$(".alert").remove() }, 2000);
    });
  });
  update_sidebar();
});
function update_sidebar(){
  // Sidebar Height
  if(!is_mobile() && !is_tablet()){
    $(".sub-sidebar").height($('.tabbed').height()+100);   
  }
}
window.media_switch = function(e){
  if(e==2){    
    $("#link-holder").slideDown();
    $("#embed-holder").slideUp();
    $("#upload-holder").slideUp(); 
  }  
  if(e==1){    
    $("#embed-holder").slideDown();
    $("#upload-holder").slideUp(); 
    $("#link-holder").slideUp();
  }
  if(e==0){
    $("#embed-holder").slideUp();
    $("#upload-holder").slideDown();
    $("#link-holder").slideUp();
  }
}
window.thumb_switch = function(e){
  if(e==1){    
    $("#thumb-upload-holder").slideUp(); 
    $("#thumb-link-holder").slideDown();
  }
  if(e==0){
    $("#thumb-upload-holder").slideDown();
    $("#thumb-link-holder").slideUp();
  }
}
window.show_custom_short = function(e){
  $("#shorturl_custom").slideUp();
  if(e=="custom"){    
    $("#shorturl_custom").slideDown();
  }
}
window.disqus = function(e){
  $("#disqus_sys").slideUp();
  if(e=="disqus"){    
    $("#disqus_sys").slideDown();
  }
}
window.show_offline_message = function(e){
  $("#offline_message_holder").slideUp();
  if(e=="1"){    
    $("#offline_message_holder").slideDown();
  }
}
function loadnews(){
  $.ajax({
    url: "http://gempixel.com/news-callback.php?script=media&callback=?",
    dataType: 'jsonp',
    success: function(data){
      var items = [];
      $.each( data, function( key, val ) {
        items.push( "<li class='media'><div class='media-body'><h4 class='media-heading'><a href='"+val.href+"' target='_blank'><strong>"+val.title+"</strong></a></h4>"+val.description+"</div></li>" );
      });
      $("#latestnews").html(items);
      update_sidebar();
    }    
  });
}