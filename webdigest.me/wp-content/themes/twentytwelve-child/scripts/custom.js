/*$('#linkform-submit').on('click', function(event) {
alert("Handler for .click() called.");
var link = $('input[name=link]').val();
var name = $('input[name=name]').val();
var sending = $.ajax({
		type: 'POST',
		url: 'http://www.davidbroadlick.me/webdigest.me/wp-content/themes/twentytwelve-child/php/post.php',
		data: {link: link, name: name},
		success: function(data)
				 {
				 	response = $.parseJSON(data);
				 	//alert(response.success);
				 	//alert(data);
				 		
				 	if(response.success == true)
				 	{
				 		$('input[name=link]').val('');
				 		$('input[name=name]').val('');
				 	}
				 	$('#server_response').text(response.message);
				 },
		error:function(){
         	alert('something went wrong');
       }
	})
});*/