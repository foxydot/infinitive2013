jQuery(document).ready(function($){$("p:empty").remove(),$("*:first-child").addClass("first-child"),$("*:last-child").addClass("last-child"),$("*:nth-child(even)").addClass("even"),$("*:nth-child(odd)").addClass("odd"),$("body").css("opacity","1");var e=$("#footer-widgets div.widget").length;$("#footer-widgets").addClass("cols-"+e),$.each(["show","hide"],function(e,s){var a=$.fn[s];$.fn[s]=function(){return this.trigger(s),a.apply(this,arguments)}}),$(".section.expandable .expand").click(function(){var e=$(this).parents(".section-body").find(".content");console.log(e),e.hasClass("open")?(e.removeClass("open"),$(this).html('MORE <i class="fa fa-angle-down"></i>')):(e.addClass("open"),$(this).html('LESS <i class="fa fa-angle-up"></i>'))}),$(".genesis-teaser").length&&$(".genesis-teaser").equalHeightColumns(),$(".equalize").length&&$(".equalize").equalHeightColumns()});