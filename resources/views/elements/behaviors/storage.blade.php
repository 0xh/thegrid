<script>
	window.GridBehaviors = window.GridBehaviors || {};

	GridBehaviors.StorageBehavior = {
		setSessionStorage: function(name, value) {
			if (typeof(Storage) !== "undefined") {
				sessionStorage.setItem(name, value);
				//return true;
			}
			//return false
		},
		getSessionStorage: function(name) {
			if (typeof(Storage) !== "undefined") {
				return sessionStorage.getItem(name);
			}
			return false
		},
		removeSessionStorage: function(name) {
			if (typeof(Storage) !== "undefined") {
				sessionStorage.removeItem(name);
			}
		},
		clearSessionStorage: function() {
			if (typeof(Storage) !== "undefined") {
				sessionStorage.clear();
			}
		}

	}
</script>