(function($){

	$(window).ready(function() {

	});

	$( document ).ready(function() {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		registerHandlers();
	});

	var registerHandlers = function(){

		$('.import_by').on('click', function() {
			if ('playlist' == $(this).val()) {
				$('#div_yt_playlist').css('display','flex');
				$('.loader-img').css('visibility','visible');
				$('#sel_playlist').prop('disabled', true);
				getYoutubePlaylists();
			} else {
				$('#sel_playlist').prop('disabled', true);
				$('.loader-img').css('visibility','hidden');
				$('#div_yt_playlist').hide();
			}
		});

		$('#channel_id').on('change', function(){
			if ('playlist' == $('.import_by:checked').val()) {
				$('.loader-img').css('visibility','visible');
				$('#sel_playlist').prop('disabled', true);
				getYoutubePlaylists();
			}
		});

		if ('playlist' == $('.import_by:checked').val()) {
			$('.loader-img').css('visibility','visible');
			$('#sel_playlist').prop('disabled', true);
			getYoutubePlaylists();
		}

		$('.initial_import_type').on('click', function(){
			if ($(this).val() == 'recent'){
				$('#div_import_recent_count').css('display','flex');
				$('#import_recent_count').prop('disabled', false).focus();
			} else {
				$('#import_recent_count').prop('disabled', true);
				$('#div_import_recent_count').hide();
			}
		})

		$('.is_scheduled input').on('click', function(){
			if ($(this).val() == '1'){
				$('.scheduled_time').css('display','flex');
			} else {
				$('.scheduled_time').hide();
			}
		})

		if ('1' == $('.is_scheduled input:checked').val()) {
			$('.scheduled_time').css('display','flex');
		} else {
			$('.scheduled_time').hide();
		}

		$('.release-now').on('click', function(){
			if ($(this).is(':checked')){
				$('.scheduled_date').prop('disabled', true);
			} else {
				$('.scheduled_date').prop('disabled', false);
			}
		})
	}

	var getYoutubePlaylists = function(){
		var channel_id = $('#channel_id').val();

		$.post(ivx.get_playlists, {
		        channel_id: channel_id
		    },
	    	function(data, status){
		        if (status == 'success') {
		        	populatePlaylists(data);
		        } else {
		        	alert('Request failed');
		        }
		    }
		);
	}

	var populatePlaylists = function(data){
		$elem = $('#sel_playlist');
		$elem.find('.list').remove();

		if (data.playlists.length == 0) {
			alert('No playlists found!');
			$('.loader-img').css('visibility','hidden');

			$('#playlist').prop('checked', false); 
			$('#channel').prop('checked', true);
			$('#sel_playlist').prop('disabled', true);
			$('#div_yt_playlist').hide();
			return;
		}

        if (data.playlists.length > 0) {
            $('input[name=playlist_name]').val(data.playlists[0].name);
        }
		$.each(data.playlists, function (i, item) {
		    $elem.append($('<option>', { 
		        value: item.id,
		        text : item.name,
		        class: 'list',
		        selected: (ivx.old_playlist == item.id)
		    }));
		});

		$('.loader-img').css('visibility','hidden');
		$('#sel_playlist').prop('disabled', false).focus();
	}

	propertyType();

	$( ".property_type" ).change(function() {
		propertyType();		
	});

	function propertyType(){
		$('.form-url').hide();
        $('.embed-sites').hide();
	}

})(jQuery);

function ConfirmDelete() {
	var x = confirm('Are you sure you want to delete?');
	return (x) ? true : false;
}


/**
 * You have this:
 * <a href="http://localhost/data/delete/1" data-method="delete" data-confirm="Are you sure?">Delete</a>
 *
 * When above link clicked, below will rendered & triggered:
 * <form method="POST" action="http://localhost/data/delete/1">
 *   <input type="text" name="_token" value="{the-laravel-token}">
 *   <input type="text" name="_method" value="delete">
 * </form>
 *
 * Now you made DELETE request via anchor link.
 */

(function() {

  var LinkMethod = {
    init: function () {
      this.links = document.querySelectorAll('a[data-method]')
      this.registerEvents()
    },

    registerEvents: function () {
      Array.from(this.links).forEach(function (l) {
        l.addEventListener('click', LinkMethod.render)
      })
    },

    render: function (e) {
      var el = this,
        httpMethod,
        form

      httpMethod = el.getAttribute('data-method').toUpperCase()

      // Ignore when the data-method attribute is not PUT or DELETE,
      if (['POST', 'PUT', 'PATCH', 'DELETE'].indexOf(httpMethod) === -1) {
        return;
      }

      // Allow user to optionally provide data-confirm="Are you sure?"
      if (el.hasAttribute('data-confirm') && ! LinkMethod.verifyConfirm(el) ) {
        e.preventDefault()
        return false;
      }

      form = LinkMethod.createForm(el)
      form.submit()

      e.preventDefault()
    },

    verifyConfirm: function (link) {
      return confirm(link.getAttribute('data-confirm'))
    },

    createForm: function (link) {
      var form = document.createElement('form')
      LinkMethod.setAttributes(form, {
        method: 'POST',
        action: link.getAttribute('href')
      })

      var laravelToken = document.querySelector("meta[name=csrf-token]").getAttribute('content');

      if (!laravelToken) {
        console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
      }

      var inputToken = document.createElement('input')
      LinkMethod.setAttributes(inputToken, {
        type: 'hidden',
        name: '_token',
        value: laravelToken
      })

      var inputMethod = document.createElement('input')
      LinkMethod.setAttributes(inputMethod, {
        type: 'hidden',
        name: '_method',
        value: link.getAttribute('data-method').toUpperCase()
      })

      form.appendChild(inputToken)
      form.appendChild(inputMethod)
      document.body.appendChild(form)

      return form
    },

    setAttributes: function (el, attrs) {
      for (var key in attrs) {
        el.setAttribute(key, attrs[key]);
      }
    }
  }

  LinkMethod.init()

})();