jQuery(document).ready(function($){$("*:first-child").addClass("first-child"),$("*:last-child").addClass("last-child"),$("*:nth-child(even)").addClass("even"),$("*:nth-child(odd)").addClass("odd");var s=$("#footer-widgets div.widget").length;$("#footer-widgets").addClass("cols-"+s),$.each(["show","hide"],function(s,a){var e=$.fn[a];$.fn[a]=function(){return this.trigger(a),e.apply(this,arguments)}}),$(".nav-footer ul.menu>li").after(function(){return!$(this).hasClass("last-child")&&$(this).hasClass("menu-item")&&"none"!=$(this).css("display")?'<li class="separator">|</li>':void 0}),$(".section.expandable .expand").click(function(){var s=$(this).parents(".section-body").find(".content");console.log(s),s.hasClass("open")?(s.removeClass("open"),$(this).html('MORE <i class="fa fa-angle-down"></i>')):(s.addClass("open"),$(this).html('LESS <i class="fa fa-angle-up"></i>'))}),$(".genesis-teaser").equalHeightColumns()});