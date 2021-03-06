<script>
	window.GridBehaviors = window.GridBehaviors || {};

	GridBehaviors.AppSettingsBehavior = {
		properties : {
			app: {
				type: Object,
				value: function() {
					return {
						app_name: "{{env('APP_NAME')}}",
						app_env: "{{env('APP_ENV')}}",
						app_debug: "{{env('APP_DEBUG')}}",
						app_url: "{{env('APP_URL')}}",
						api_url: "{{env('APP_API_URL')}}",
						api_socket_host: "{{env('APP_SOCKET_HOST')}}",
						api_socket_port: "{{env('APP_SOCKET_PORT')}}",
					};
				}
			},
			client: {
				type: Object,
				value: function() {
					return {
						client_id: "{{env('CLIENT_ID')}}",
						client_secret: "{{env('CLIENT_SECRET')}}"
					};
				}
			}
		},
	}
</script>