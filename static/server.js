$(document).ready(function(){
 /**
   * Server Requests
   **/ 
  $(document).on('click','.this-action',function(e){
      e.preventDefault();
      var action = $(this).data("action");
      var data = $(this).data("data");
      $.ajax({
        type: "POST",
        url: appurl+"/server",
        data: "action="+action+"&data="+data+"&token="+token,                
        success: function (html) {           
          $(".this-return-data").html(html);
        }
      });       
  });
  /**
   * Report Page/Media
   */
  $("#this-report,#this-playlist-settings,#this-playlist").click(function(e){
    e.preventDefault();
    $(this).modal();
  });
  $(document).on('submit',"#report-form",function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: appurl+"/server",
      data: "action=report&data="+$(this).find("input").val()+"&token="+token+"&report="+$(this).find("select").val(),                  
      success: function (html) {           
        $(".this-return-data").html(html);
      }
    })    
  });
 /**
   * Add Comment
   **/ 
  $("#comment-form").submit(function(e){
      e.preventDefault();
      var data = $(this).serialize();
      data = data +"&user="+$(".video-author").data("id");
      $.ajax({
        type: "POST",
        url: appurl+"/server",
        data: data,
        beforeSend: function() {
          $(".return-data").html("");
        },               
        success: function (html) {           
          $(".this-return-data").html(html);
        }
      });       
  });
  // Reply to a comment + Cancel reply
  $("a.reply").click(function(e){
    e.preventDefault();
    var id = $(this).data("parent");
    $("#comment-parentid").val(id);
    $("#comment-form textarea").focus().val("@"+$(this).data("user")+" ");
    $(".replyto").html("Reply to <strong>"+$(this).data("user")+"</strong> <a href='#cancel' id='cancelreply'>(cancel)</a>");
  }).smoothscroll({holder: "#comment-form"});
  $(document).on("click","a#cancelreply",function(e){
    $(".replyto").html("");
    $("#comment-parentid").val("0");
  });
  /**
   * Submit URL
   */
  $("#fetch-media").click(function(e){
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: appurl+"/server",
      data: "action=submit&url="+$("#url").val()+"&token="+token,
      beforeSend: function() {
        $(".return-data").html("");
        $(document).loader();
      },                
      success: function (html) {     
        $(document).loader({close: 1});      
        $(".this-return-data").html(html);
      }
    });       
  });
  if(/[?&]url=/.test(location.href)){
    $.ajax({
      type: "POST",
      url: appurl+"/server",
      data: "action=submit&url="+$("#url").val()+"&token="+token,
      beforeSend: function() {
        $(".return-data").html("");
        $(document).loader();
      },                
      success: function (html) {       
        $(document).loader({close: 1});        
        $(".this-return-data").html(html);
      }
    });      
  }
  // Click "Short Link" > Generate Short URL
  var b = $("#shortlink").val(); 
  $(".shorten").click(function(e) {
    e.preventDefault();
    if (!$(this).hasClass('active')) {
      var a = $("#shortlink").val();
      var t = $(this).data("type");
      if(t == "system"){
        $('#shortlink').val($(this).attr('data-short'));
      }else{
        $.post(appurl +"/server", {action: 'shorten', url: a, token: token}, function (r) {
          $('#shortlink').val(r).select();
        });
      }
      $(this).addClass('active');
    } else {
      $('#shortlink').val(b);
      $(this).removeClass('active');
    }
  });  
  /**
   * Live Search
   */
  $("#search-input").keyup(function(e){  
    var v = $(this).val();
    if(v.length >= 3){
      $.ajax({
        type: "POST",
        url: appurl+"/server",
        data: "action=livesearch&value="+v+"&token="+token,               
        success: function (html) {           
          if(html.length > 0){
            var pos=$("#search-input").offset();
            var w=$("#search-input").width();
            var x=pos.left;
            var y=pos.top + 30;
            $(".this-return-data").html(html); 
            $("#live-search").css({top:y,left:x,width: w});
          }
        }
      }); 
    }else{
      $("#live-search").remove();
    }
  });
  $("#upload form").submit(function(e){      
    var t = $(this);
    var error = 0;
    t.find(".has-error").removeClass(".has-error");
    // Check title
    if(t.find("#title").val().length == 0){
      t.find("#title").parent(".form-group").addClass("has-error");
      error = 1;
    }
    // Check Media
    if(t.parent("#media").length == 1 && t.find("#upload").val().length == 0){
      t.find("#upload").parent(".form-group").addClass("has-error");
      error = 1;
    }    
    // Handle Error
    if(error == 1){
      e.preventDefault();
      return;
    }
    t.find("button").hide();
    $(document).loader();    
  });
})