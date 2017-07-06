<dom-module id="grid-mobile-header">
	<style>
    :host {
      background: linear-gradient(180deg, #212121 35.36%, rgba(33, 33, 33, 0) 100%);
      background-blend-mode: darken;
      width: 100%;
      height: 150px;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 3;
    }

    paper-toolbar {
      background: transparent;
    }

    paper-toolbar paper-icon-button {
      color: #FF3B30;
    }

    paper-toolbar #topBar,
    paper-toolbar #middleBar {
      padding: 0 8px;
    }

    .title-text {
      padding: 0 8px;
      color: #FFFFFF;
    }
    .title-text h1 {
      margin: 0;
      font-size: 34px;
    }
    .title-text .data {
      font-size: 11px;
    }
    .pull-down {
      text-align: center;
      width: 100%
    }

	</style>
	<template>
    <paper-toolbar class="tall">
      <paper-icon-button slot="top" icon="menu" on-tap="openDrawer"></paper-icon-button>
      <span slot="top" class="title"></span>
      <paper-icon-button slot="top" icon="maps:my-location" on-tap="setCurrentLocation"></paper-icon-button>
      <paper-icon-button slot="top" icon="filter-list"></paper-icon-button>
      <div slot="middle" class="middle title-text">
        <small clas="date">July 11, 2016</small>
        <h1>The Grid</h1>
      </div>
      <div slot="bottom" class="bottom pull-down">
        <paper-icon-button icon="hardware:keyboard-arrow-down"></paper-icon-button>
      </div>
    </paper-toolbar>
	</template>
</dom-module>
<script>
	(function(){

		Polymer({
			is: 'grid-mobile-header',
      behaviors: [GridBehaviors.FoldBehavior],
      openDrawer: function() {
        this.drawer.open();
      },
      setCurrentLocation: function() {
        console.log('setting current location');
        this.view.setCurrentLocation();
      }

		});
	}());
</script>
