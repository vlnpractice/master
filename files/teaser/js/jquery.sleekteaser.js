//Sleek Sweep Gallery Pack
// Date: 12/30/2013
// Author: Seven
// Copyright: All rights reserved to Seven
(function($)
{
	//slider object
	var object;
	//interface for seven slider
	$.fn.sleekteaser = function(options){
		 object=new sleekteaser({
			 handle:$(this),
			 option:options
		 });	
		return object;
	};
	//main function
	function sleekteaser(arg)
	{
		//global variable
		//slide handler
		var handle;
		//options
		var option;
		//item width
		var itemwidth;
		//timer handler for autoplay
		var timer;
		//autoplay flag
		var a_flag;
		//carousel flag
		var c_flag;
		//timer step
		var t_val;
		//slide indexes
		var current_index,target_index;
		//music index
		var music_index;
		//lock flag
		var lock;
		//mouse capture flag/touch
		var mpflag,cpflag,tpflag;
		//swipe direction flag key
		var sd_flag;
		//progressbar color
		var pcolor;
		//mouse offset buffer
		var mp_temp,tp_temp,cr_temp;
		//thumbnail flag
		var t_flag;
		//original rate width vs height
		var rate;
		//overall slide length
		var length;
		//thumbnail width
		var thumb_width;
		//audio
		var audio;
		//default parameters
		var defaults={
			//slider width
			width:				800,
			//slider height
			height:				300,
			//carousel  number
			num_carousel:		5,
			//auto play - disabled
			autoplay:			false,
			//interval(second) - disabled
			interval:			5,
			//fullwidth/fullscreen - disabled
			mode:			0,
			//responsive
			responsive:			true,
			//progressbar - disabled
			progressbar:		true,
			//caption animation - disabled
			caption_animation:	0,
			//animation - disabled
			animation:			0,
			//repeat mode
			repeat_mode:		true,
			//skin type
			skin:				'light',
			//path - disabled
			path:				'',
			//lightbox - disabled
			lightbox: 			false,
			//pause on hover  - disabled
			pause_on_hover:		true,
			//swipe mode
			swipe:				false,
			//keyboard mode
			keyboard:			false,
			//custom event
			onanimstart:		function(){return false;},
			//custom event
			onanimend:			function(){return false;},
			//custom event
			onvideoplay: 		function(){return false;},
			//custom event
			onslidechange:		function(){return false;}
		};
		
		//slide handler
	  	handle=arg.handle;
	  	//option values
	  	option=$.extend({}, defaults, arg.option || {});
		//initialization
		sleek_init();
		//init func
		function sleek_init()
		{
			//set height
			handle.find("#sleek_viewport").css("width",option.width);
			//variable initialization
			lock=t_val=0;
			current_index=target_index=music_index=0;
			mpflag=tpflag=t_flag=a_flag=sd_flag=c_flag=cpflag=0;
			length=handle.find(".sleek_slide").length;
			//gallery list
			handle.find("#sleek_viewport").append("<div id='sleek_subviewport'><div id='sleek_frame'></div></div>");
			handle.find("#sleek_viewport").append(handle.find("#left_btn")).append(handle.find("#right_btn"));
			handle.find("#sleek_frame").css("width",((option.width-20*(option.num_carousel+1))/option.num_carousel)*length+20*(length+1));
			handle.find("#sleek_viewport").addClass(option.skin);
			//init each slide settings
			handle.find(".sleek_slide").each(function(i)
			{
				var cache=$(this);
				var src=cache.attr("image-src");
				var caption=cache.attr("data-caption");
				var des=cache.attr("data-description");
				var video=cache.attr("video-src");
				var href=cache.attr("data-link");
				if(typeof(href)=='undefined') href="#";
				if(typeof(video)=='undefined') video="";
				cache.append("<div class='sleek_img_cont'><img class='sleek_image' src='"+src+"' data-src='"+video+"' style='width:140px;height:140px;' /></div>");
				if(typeof(caption)!='undefined') cache.append("<div class='sleek_caption'><div><a class='sleek_title' href='"+href+"'>"+caption+"</a></div><div><a class='sleek_des'>"+des+"</a></div></div>");
				if(video!="")	cache.find(".sleek_img_cont").append("<img class='sleek_video_play' src='"+option.path+"img/play.png'/>");
				handle.find("#sleek_frame").append("<div class='sleek_item'>"+cache.html()+"</div>");
				cache.find(".sleek_image").load(function()
				{
					//image is fully loaded
					$(this).addClass("active");
					
				});
			});
			/* sleek setup */
			sleek_setup();			
			//thumbnail
			itemwidth=(option.width-20*(option.num_carousel+1))/option.num_carousel;
			handle.find(".sleek_item").css("width",itemwidth);
		}
		function sleek_respond()
		{
		}
		function sleek_setup()
		{
		}
		sleekteaser.prototype.prev=function(){
			lock=1;
			option.onanimstart();
			current_index=(current_index-1+length)%length;
			var index=current_index;
			var caption=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-caption");
			var des=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-description");
			handle.find(".sleek_item").removeClass("active");
			handle.find(".sleek_item:nth-child("+(index+1)+")").addClass("active");
			handle.find("#sleek_title_header").html(caption);
			handle.find("#sleek_title_content").html(des);
			handle.find("#sleek_frame").anima({"margin-left":-index*option.width+"px"},600,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();
				}
			});	
		}
		sleekteaser.prototype.next=function(){
			option.onanimstart();
			current_index=(current_index+1)%length;
			var index=current_index;
			var caption=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-caption");
			var des=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-description");
			handle.find(".sleek_item").removeClass("active");
			handle.find(".sleek_item:nth-child("+(index+1)+")").addClass("active");
			handle.find("#sleek_title_header").html(caption);
			handle.find("#sleek_title_content").html(des);
			lock=1;
			handle.find("#sleek_frame").anima({"margin-left":-index*option.width+"px"},600,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();								
				}
			});
		}
		/****************************************************************/
		/**********************jQuery Events*****************************/
		/****************************************************************/
		//bind event handler
		$(document).bind("dragstart", function() { return false; });
		$(document).keyup(function(e) {
			var bufferindex;
  		    if(lock==1) return;
			if(option.keyboard==true)
			{
				  if(e.keyCode == 37) { // left										
					lock=1;
					option.onanimstart();
					current_index=(current_index-1+length)%length;
					var index=current_index;
					var caption=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-caption");
					var des=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-description");
					handle.find(".sleek_item").removeClass("active");
					handle.find(".sleek_item:nth-child("+(index+1)+")").addClass("active");
					handle.find("#sleek_title_header").html(caption);
					handle.find("#sleek_title_content").html(des);
					handle.find("#sleek_frame").anima({"margin-left":-index*option.width+"px"},600,".19,1,.22,1",
					{
						complete:function(){
							$(this).stopAnima();
							lock=0;
							option.onanimend();
							option.onslidechange();
						}
					});
				  }
				  else if(e.keyCode == 39) { // right
					option.onanimstart();
					current_index=(current_index+1)%length;
					var index=current_index;
					var caption=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-caption");
					var des=handle.find(".sleek_slide:nth-child("+(index+1)+")").attr("data-description");
					handle.find(".sleek_item").removeClass("active");
					handle.find(".sleek_item:nth-child("+(index+1)+")").addClass("active");
					handle.find("#sleek_title_header").html(caption);
					handle.find("#sleek_title_content").html(des);
					lock=1;
					handle.find("#sleek_frame").anima({"margin-left":-index*option.width+"px"},600,".19,1,.22,1",
					{
						complete:function(){
							$(this).stopAnima();
							lock=0;
							option.onanimend();
							option.onslidechange();								
						}
					});
				  }	
				  else if(e.keyCode==27)
				  {	
					  handle.find("#seven_sublightbox").animate({
							"opacity":0,
							"scale":0.1,
					  },
					  {
							duration:200,
							easing:"easeOutSine",
							complete:function()
							{
								handle.find("#seven_lightbox").remove();  
							}
					  });
				  }
			}
		});
		var vbuffer;
		handle.find("#sleek_subviewport").mousedown(function(e){
			if(lock==1) return;
			mp_temp=e.pageX;
			mpflag=1;
			cpflag=0;
			vbuffer=parseFloat(handle.find("#sleek_frame").css("margin-left"));
		}).mousemove(function(e){
			if(mpflag==0) return;
			cpflag=1;
			var offset=e.pageX-mp_temp;
			handle.find("#sleek_frame").css("margin-left",vbuffer+offset);
		}).mouseup(function(e){
			if(mpflag==0) return;
			mpflag=0;
			lock=1;
			var offset=e.pageX-mp_temp;
			var left=vbuffer+offset;			
			var width=handle.find("#sleek_subviewport").width();
			var t_width=handle.find("#sleek_frame").width();
			var move_distance=0;
			if(offset<0)
			{
				current_index=(current_index+option.num_carousel);
				if(current_index>length-1) current_index-=option.num_carousel;
				move_distance=-(current_index)*(itemwidth+20);
			}
			else
			{
				current_index=(current_index-option.num_carousel);
				if(current_index<0) current_index=0;
				move_distance=-(current_index)*(itemwidth+20);
			}
			option.onanimstart();
			handle.find("#sleek_frame").anima({"margin-left":move_distance+"px"},400,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();					
				}
			});
		}).mouseleave(function(e){
			if(mpflag==0) return;
			mpflag=0;
			lock=1;
			var offset=e.pageX-mp_temp;
			var left=vbuffer+offset;			
			var width=handle.find("#sleek_subviewport").width();
			var t_width=handle.find("#sleek_frame").width();
			var move_distance=0;
			if(offset<0)
			{
				current_index=(current_index+option.num_carousel);
				if(current_index>length-1) current_index-=option.num_carousel;
				move_distance=-(current_index)*(itemwidth+20);
			}
			else
			{
				current_index=(current_index-option.num_carousel);
				if(current_index<0) current_index=0;
				move_distance=-(current_index)*(itemwidth+20);
			}
			option.onanimstart();
			handle.find("#sleek_frame").anima({"margin-left":move_distance+"px"},400,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();					
				}
			});
		});
		//touch event
		handle.on("touchstart","#sleek_viewport",function(e){
			if(lock==1) return;
			mp_temp=e.originalEvent.touches[0].pageX;
			mpflag=1;
			cpflag=0;
			vbuffer=parseFloat(handle.find("#sleek_frame").css("margin-left"));
		}).on("touchmove","#sleek_viewport",function(e){
			if(mpflag==0) return;
			cpflag=1;
			var offset=e.originalEvent.changedTouches[0].pageX-mp_temp;
			handle.find("#sleek_frame").css("margin-left",vbuffer+offset);
		}).on("touchend","#sleek_viewport",function(e){
			if(mpflag==0) return;
			mpflag=0;
			lock=1;
			var offset=e.originalEvent.changedTouches[0].pageX-mp_temp;
			var left=vbuffer+offset;			
			var width=handle.find("#sleek_subviewport").width();
			var t_width=handle.find("#sleek_frame").width();
			var move_distance=0;
			if(offset<0)
			{
				current_index=(current_index+option.num_carousel);
				if(current_index>length-1) current_index-=option.num_carousel;
				move_distance=-(current_index)*(itemwidth+20);
			}
			else
			{
				current_index=(current_index-option.num_carousel);
				if(current_index<0) current_index=0;
				move_distance=-(current_index)*(itemwidth+20);
			}
			option.onanimstart();
			handle.find("#sleek_frame").anima({"margin-left":move_distance+"px"},400,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();					
				}
			});
		});
		//left/right button
		handle.find("#left_btn").click(function(){
			if(lock==1) return;
			lock=1;
			var width=handle.find("#sleek_subviewport").width();
			var t_width=handle.find("#sleek_frame").width();
			var move_distance=0;
			current_index=(current_index-option.num_carousel);
			if(current_index<0) current_index=0;
			move_distance=-(current_index)*(itemwidth+20);
			option.onanimstart();
			handle.find("#sleek_frame").anima({"margin-left":move_distance+"px"},400,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();					
				}
			});
		});
		handle.find("#right_btn").click(function(){
			if(lock==1) return;
			lock=1;
			var width=handle.find("#sleek_subviewport").width();
			var t_width=handle.find("#sleek_frame").width();
			var move_distance=0;
			current_index=(current_index+option.num_carousel);
			if(current_index>length-1) current_index-=option.num_carousel;
			move_distance=-(current_index)*(itemwidth+20);
			option.onanimstart();
			handle.find("#sleek_frame").anima({"margin-left":move_distance+"px"},400,".19,1,.22,1",
			{
				complete:function(){
					$(this).stopAnima();
					lock=0;
					option.onanimend();
					option.onslidechange();					
				}
			});
		});
		/* video play */
		handle.on("click",".sleek_video_play",function()
		{
			option.onvideoplay();
			var url=$(this).parent().find(".sleek_image").attr("data-src");
			handle.append("<div id='sleek_lightbox'><div id='sleek_video_display'><iframe src='"+url+"'></iframe><div id='sleek_video_close'></div></div></div>");
			//lightbox resize
			if(option.width<700) handle.find("#sleek_video_display").css("width",option.width-42).css("left",0).css("margin-left",0);			
		});
		/* lightbox close */
		handle.on("click","#sleek_video_close",function()
		{
			handle.find("#sleek_lightbox").fadeOut("fast",function(){$(this).remove();});
		});	
		$(window).resize(function(){
			sleek_respond();								  
		});
	}
})(jQuery);