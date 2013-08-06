$('.tree li').each(function(){
        if($(this).children('ul').length > 0){	//Ajoute l'icone "+" aux parents.
                $(this).addClass('parent');
        }
});

var hrefs = [];
$('.gclink li > a').each(function(index){
	hrefs[index] =  $(this).attr("href");	//explosion des urls (liens de gestion de catégorie)
});

$('.gclink li > a').click(function(){
	var attr = $(this).attr('href');
   	if(attr.length < 30){
	   		alert('Veuillez selectionner une catégorie avant !');	//Si un lien est clicé sans avoir au préalable sélectionner une catégorie
	   		return false;
       }
});

$('.tree li.parent > p').click(function(){
        $(this).parent().toggleClass('active');
        $(this).parent().children('ul').slideToggle('fast');	//Un clic sur un parent déclenche le slidetoggle de ses enfants.
});

$('.treeLink li > a').click(function(){
	$('.treeLink li > a').each(function(){	//On retire le style "selected" de tout les liens.
		$(this).removeAttr("id");
	});
	
	$(this).attr('id', 'selected');		//Et on le rajoute sur celui sur lequel on viens de clicé.
	var txt = $(this).attr('data-slug');	//Ajout du slug pour le controller.
	
	$('.gclink li > a').each(function(index){
		$(this).attr('href', hrefs[index]+txt); //recréation du lien.
	});
	
	$('#new').each(function(){
		var attr = $(this).attr("href");
		
		if ( attr.charAt( attr.length-1 ) == '/')	//Recréation du lien d'ajout de fichier.
		{
			$(this).attr('href', attr+txt);
		}
		else
		{
			var split = $(this).attr("href").split('/');
			split.pop();
			
			var length = split.length;
			var href = '';
			for (var i = 1; i < length; i++) {
				element = split[i];
				href = href+'/'+element;
			}
			
			$(this).attr('href', href+'/'+txt); //recréation du lien.
		}
	});
});

/////////////Requete ajax pour les fichiers
$('.treeLink li > a').click(function(event) {
	event.preventDefault();
	var path = $(this).attr('href');
	$('.filesrefrsh').html('');
	if(path != '#')
	{
		$(".filesrefrsh").addClass("loading");
		
		$.ajax({
		    type: 'POST',
		    url: path,
		    data: { ajax: 'on' },
		    success: function(data) {
		    	$('.filesrefrsh').html(data);
		    	$(".filesrefrsh").removeClass("loading"); 
		    }
		});
	}
});

/////////////Requete ajax pour users
$('.ajax-popup-link').magnificPopup({
	  type: 'ajax'
	});

	
	
	
	
	
	
	