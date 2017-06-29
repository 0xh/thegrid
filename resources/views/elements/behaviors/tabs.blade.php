<script>
	window.GridBehaviors = window.GridBehaviors || {};

	GridBehaviors.TabsBehavior = {
		properties: {
			selectedTab: {
	          type: String,
	          value: "auth",
	          notify: true,
	          observer: '_initialize'
	        },
		},
		created: function() {
			// console.log('Tab behavior is created for ', this);
		},
		_initialize: function() {
			// console.log('selected tab:', this.selectedTab);
		}
	}
</script>