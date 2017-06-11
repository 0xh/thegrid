<script>
	window.GridBehaviors = window.GridBehaviors || {};

	GridBehaviors.PageBehavior = {
		properties : {
			opened: {
	          type: Boolean,
	          value: false,
	          notify: true,
	          reflectToAttribute: true,
	          observer: '_viewChanged'
	        },
	        pages: {
	        	type: Object,
	        	value: function() {
	        		return {
	        			'/': '',
	        			'/login': 'auth',
	        			'/register': 'register',
	        			'/password/reset': 'forgot-password',
	        			'/inbox': 'inbox',
	        			'/jobs': 'jobs',
	        		}
	        	}
	        }
		},
		get getTab() {
			return this.pages[window.location.pathname] || '';
		},
		pushState: function(e) {
			console.log(this, e);
		}
	}
</script>