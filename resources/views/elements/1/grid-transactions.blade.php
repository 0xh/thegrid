<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/elements/the-grid/grid-behaviors/grid-fold-behavior.html">

<dom-module id="grid-transactions">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;	
		}
	</style>
	<template>
		<paper-header-panel class="flex">
		    <paper-toolbar>
		      <div class="flex">Transactions</div>
		      <paper-icon-button icon="chevron-left" onClick="secondFold.close()"></paper-icon-button>
		    </paper-toolbar>
		    <div>
		    	content
		    	<paper-icon-button icon="help" on-click="callParent"></paper-icon-button>
		    </div>
		 </paper-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-transactions',
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			}
		});
	}());
</script>
