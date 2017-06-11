<script>
	window.GridBehaviors = window.GridBehaviors || {};

	GridBehaviors.TabsBehavior = {
		properties: {
			selectedTab: {
	          type: String,
	          value: "auth",
	          notify: true,
	          observer: '_tabChanged'
	        }
		},
		created: function() {
			// console.log('Tab behavior is created for ', this);
		},
		_tabChanged: function() {
			// console.log('selected tab:', this.selectedTab);
		}
	}
</script>