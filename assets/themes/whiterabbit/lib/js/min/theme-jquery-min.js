jQuery(document).ready(function($){$("p:empty").remove(),$("body *:first-child").addClass("first-child"),$("body *:last-child").addClass("last-child"),$("body *:nth-child(even)").addClass("even"),$("body *:nth-child(odd)").addClass("odd"),$("body").css("opacity","1");var e=$("#footer-widgets div.widget").length;$("#footer-widgets").addClass("cols-"+e),$.each(["show","hide"],function(e,s){var t=$.fn[s];$.fn[s]=function(){return this.trigger(s),t.apply(this,arguments)}}),$(".section.expandable .expand").click(function(){var e=$(this).parents(".section-body").find(".content");console.log(e),e.hasClass("open")?(e.removeClass("open"),$(this).html('MORE <i class="fa fa-angle-down"></i>')):(e.addClass("open"),$(this).html('LESS <i class="fa fa-angle-up"></i>'))}),$(".genesis-teaser").length&&$(".genesis-teaser").equalHeightColumns(),$(".equalize").length&&$(".equalize").equalHeightColumns();var s=$(".pre-header").outerHeight(),t=$(".site-header").outerHeight();$(window).width()>480?($(".pre-header").sticky(),$(".site-header").sticky({topSpacing:s})):$(".site-header").sticky(),$(window).scroll(function(){0==$(window).scrollTop()&&$(".sticky-wrapper").css("height","auto")})});