<link rel="import" href="/bower_components/polymer/polymer.html">
<link rel="import" href="/bower_components/app-layout/app-layout.html">
<link rel="import" href="/bower_components/iron-flex-layout/iron-flex-layout-classes.html">

<link rel="import" href="/bower_components/paper-material/paper-material.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">

<link rel="import" href="/bower_components/paper-scroll-header-panel/paper-scroll-header-panel.html">
<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/bower_components/paper-input/paper-input.html">
<link rel="import" href="/bower_components/paper-button/paper-button.html">

<link rel="import" href="/bower_components/iron-icons/iron-icons.html">
<link rel="import" href="/bower_components/iron-icons/social-icons.html">
<link rel="import" href="/bower_components/iron-icons/communication-icons.html">
<link rel="import" href="/bower_components/iron-media-query/iron-media-query.html">

<link rel="import" href="/bower_components/paper-icon-button/paper-icon-button.html">
<link rel="import" href="/bower_components/paper-menu/paper-menu.html">
<link rel="import" href="/bower_components/paper-item/paper-item.html">
<link rel="import" href="/bower_components/paper-item/paper-icon-item.html">
<link rel="import" href="/bower_components/paper-item/paper-item-body.html">
<link rel="import" href="/bower_components/paper-progress/paper-progress.html">
<link rel="import" href="/bower_components/paper-dialog/paper-dialog.html">
<link rel="import" href="/bower_components/neon-animation/animations/scale-up-animation.html">
<link rel="import" href="/bower_components/neon-animation/animations/scale-down-animation.html">


<link rel="import" href="/bower_components/gold-email-input/gold-email-input.html">
<link rel="import" href="/bower_components/gold-phone-input/gold-phone-input.html">

<!-- <link rel="import" href="/elements/first-element.html" /> -->
{{-- Behaviors --}}
<link rel="import" href="/grid-elements/behaviors.app-settings">
<link rel="import" href="/grid-elements/behaviors.fold">
<link rel="import" href="/grid-elements/behaviors.tabs">
<link rel="import" href="/grid-elements/behaviors.map">
<link rel="import" href="/grid-elements/behaviors.page">
<link rel="import" href="/grid-elements/behaviors.storage">

<!-- Custom Elements -->
<link rel="import" href="/grid-elements/grid-shared-styles">
<link rel="import" href="/grid-elements/grid-drawer">
<link rel="import" href="/grid-elements/grid-view">

<!-- second fold -->
<link rel="import" href="/grid-elements/grid-second-fold">

<!-- third fold -->
<link rel="import" href="/grid-elements/grid-third-fold">

<!-- scripts -->
<link rel="import" href="/grid-elements/scripts.axios">
<link rel="import" href="/grid-elements/scripts.socket-io">
@if (Auth::guest())
                           
@else
    {{ Auth::user()->name }}
@endif