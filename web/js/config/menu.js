$(document).ready(function(){
	_.each($('#main_menu a:not([href=""])'), function(a){
		if (a.href === location.href){
			_.each($(a).parents(), function(p){
				$(p).addClass('active')
				if (!$(p).parents('.classic-menu-dropdown').length){
					return
				}
			// $(a).parents('.classic-menu-dropdown').addClass('active');
			})
			return
		}
	})
})