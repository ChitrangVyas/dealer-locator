var _____WB$wombat$assign$function_____ = function(name) {return (self._wb_wombat && self._wb_wombat.local_init && self._wb_wombat.local_init(name)) || self[name]; };
if (!self.__WB_pmw) { self.__WB_pmw = function(obj) { this.__WB_source = obj; return this; } }
{
  let window = _____WB$wombat$assign$function_____("window");
  let self = _____WB$wombat$assign$function_____("self");
  let document = _____WB$wombat$assign$function_____("document");
  let location = _____WB$wombat$assign$function_____("location");
  let top = _____WB$wombat$assign$function_____("top");
  let parent = _____WB$wombat$assign$function_____("parent");
  let frames = _____WB$wombat$assign$function_____("frames");
  let opener = _____WB$wombat$assign$function_____("opener");

$(document).ready(function () {
    $('.c-burger').on('click', function () {
        if (!$('.nav-menu').is(':visible')) {
            $('.nav-menu').slideDown(200);
        } else {
            $('.nav-menu').slideUp(200);
        }
    });
    $('.c-burger').on('click', function (e) {
        e.stopPropagation();
    });

});

$(document).click(function () {
    
    if($(window).width() < 768)
{
    $('.nav-menu').slideUp(200); //hide the button
}
});



}
/*
     FILE ARCHIVED ON 18:22:24 Feb 16, 2021 AND RETRIEVED FROM THE
     INTERNET ARCHIVE ON 18:53:39 Jul 04, 2023.
     JAVASCRIPT APPENDED BY WAYBACK MACHINE, COPYRIGHT INTERNET ARCHIVE.

     ALL OTHER CONTENT MAY ALSO BE PROTECTED BY COPYRIGHT (17 U.S.C.
     SECTION 108(a)(3)).
*/
/*
playback timings (ms):
  captures_list: 193.658
  exclusion.robots: 0.204
  exclusion.robots.policy: 0.196
  cdx.remote: 0.053
  esindex: 0.007
  LoadShardBlock: 156.718 (3)
  PetaboxLoader3.datanode: 184.693 (4)
  load_resource: 570.982
  PetaboxLoader3.resolve: 523.387
*/