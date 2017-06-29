<script>
	window.GridBehaviors = window.GridBehaviors || {};

	GridBehaviors.FoldBehavior = {
		properties : {
			opened: {
	          type: Boolean,
	          value: false,
	          notify: true,
	          reflectToAttribute: true,
	          observer: '_viewChanged'
	        },
	        isLoading: {
	        	type: Boolean,
	        	value: true
	        },
	        isInit: {
	        	type: Boolean,
	        	value: false
	        }
		},
		created: function() {
			//console.log('Behavior is created for ', this);
		},
		get grid() {
			return document.getElementById('the-grid');
		},
		get parent() {
			// return this.domHost;
			return document.getElementById('the-grid');
		},
		get secondFold() {
			return this.parent.$.secondFold;
		},
		get thirdFold() {
			return this.parent.$.thirdFold;
		},
		get drawer() {
			return this.parent.$.drawer;
		},
		get view() {
			return this.parent.$.view;
		},
		get map() {
			return this.view.$.map;
		},
		open: function() {
			this.opened = true;
			this._onOpen();
		},
		close: function() {
			this.opened = false;
			this._onClose();
		},
		toggle: function() {
			this.opened = !this.opened;
			this._onToggle();
		},
		_onOpen: function() {
			// abstract
		},
		_onClose: function() {
			// abstract
		},
		_onToggle: function() {
			// abstract
		},
		_viewChanged: function() {
			if( this.opened ) {
				this._onOpen();
			} else {
				this._onClose();
			}
		},
		init: function() {
			// abstract
		}
	}
</script>