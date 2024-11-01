jQuery(document).ready(
	function($) {
		$( '#display-config' ).click(
			function(e) {
				$( '#wpstack-connect-modal-configuration' ).addClass( 'show' );
				$( '#wpstack-connect-modal-configuration' ).show( 300 );
			}
		);

		$( '.auto-connect' ).click(
			function(e) {
				var autoConnectBtn = $(this);
				autoConnectBtn.text('Connecting ...');
				$.ajax(
					{
						type: "post",
						url: wpstack_object.ajax_url,
						data: {
							action : 'auto_connect'
						}
					}
				).done(
					function(data) {
						var res = JSON.parse(data)
						if (res.status == 'success') {
							if (res.is_redirect) {
								setTimeout(() => {
									location.href = res.redirect_url
								}, 2000);
							}
							autoConnectBtn.text('Redirecting ....')
							wpstack_toast(res.status, res.message)
						} else {
							autoConnectBtn.text('Auto connect')
							update_wpstack_message_status()
							wpstack_toast(res.status, res.message)
						}
					}
				).fail(
					function(jqXHR, textStatus) {
						autoConnectBtn.text('Auto connect')
						update_wpstack_message_status()
					}
				);
			}
		);

		function wpstack_toast(status, message)
		{
			var wpstackToast = $('#wpstack-connect-toast');
			var toastMessage = wpstackToast.find('#wpstack-connect-toast-message')
			wpstackToast.on('click', '[data-dismiss=toast]', function() {
				wpstackToast.hide();
				toastMessage.html('');
				if (status === 'success') {
					wpstackToast.removeClass('fade show bg-success');
				} else {
					wpstackToast.removeClass('fade show bg-danger');
				}
			})
			if (status === 'success') {
				wpstackToast.addClass( 'fade show bg-success' );
			} else {
				wpstackToast.addClass( 'fade show bg-danger' );
			}
			toastMessage.html(message);
			wpstackToast.show( 300 );
		}

		$( '[data-dismiss=modal]' ).click(
			function(e){
				$( '#wpstack-connect-modal-configuration' ).removeClass( 'show' );
				$( '#wpstack-connect-modal-configuration' ).hide();
			}
		);

		$('.btn-cp-pk').click(function (e) {
			var button = $(this);
			var copyText = button.parent().prev( '.public-key' );
			copyText.select();
			var content = copyText.val();
		
			if (window.isSecureContext && navigator.clipboard) {
				navigator.clipboard.writeText(content)
					.then(function () {
						showCopiedMessage(button);
					})
					.catch(function (err) {
						console.error( 'Unable to copy to clipboard:', err );
					});
			} else {
				unsecuredCopyToClipboard(content);
				showCopiedMessage(button);
			}
		});

		function showCopiedMessage(button) {
			button.text( 'Copied!' );
			setTimeout(function () {
				button.text( 'Copy' );
			}, 1000);
		}		

		function unsecuredCopyToClipboard(content) {
			var tempInput = $( '<input>' );
			$( 'body' ).append(tempInput);
			tempInput.val(content).select();
			document.execCommand( 'copy' );
			tempInput.remove();
		}

		$( '[data-connected=disconnected]' ).click(
			function(e) {
				$.ajax(
					{
						type: "post",
						url: wpstack_object.ajax_url,
						data: {
							action : 'disconnected'
						}
					}
				).done(
					function(data) {
						var result = JSON.parse( data );
						$( '.public-key ').each(function() {
							$(this).val(result.public_key);
						});
						$( '.modal-footer' ).html( '<button type="button" class="btn btn-primary disabled"><i class="fa fa-link"></i> Connect</button>' );
					}
				).fail(
					function(jqXHR, textStatus) {

					}
				);
			}
		);

		function update_wpstack_message_status() {
			$.ajax(
				{
					type: "post",
					url: wp_stack_object.ajax_url,
					data: {
						action: ' update_message_status '
					}
				}
			).done(
				function (data) {
					var result = JSON.parse(data);
					if (result.reload) {
						location.reload();
					}
				}
			);
		}
	}
);
