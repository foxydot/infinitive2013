jQuery(document).ready(function($){$("main .publication-list li").each(function(){if($(this).innerHeight()>187){var i=($(this).innerHeight()-187)/2+10;$(this).find(".news-logo").css("padding-top",i+"px").css("padding-bottom",i+"px")}})});