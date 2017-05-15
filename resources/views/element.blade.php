<link rel="import" href="/bower_components/polymer/polymer.html">
<link rel="import" href="/bower_components/app-layout/app-layout.html">
<link rel="import" href="/bower_components/iron-flex-layout/iron-flex-layout.html">
<link rel="import" href="/bower_components/iron-flex-layout/iron-flex-layout-classes.html">

<link rel="import" href="/bower_components/paper-material/paper-material.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">

<link rel="import" href="/bower_components/iron-icons/iron-icons.html">
<link rel="import" href="/bower_components/iron-icons/social-icons.html">
<link rel="import" href="/bower_components/iron-icons/communication-icons.html">

<link rel="import" href="/bower_components/paper-icon-button/paper-icon-button.html">
<link rel="import" href="/bower_components/paper-menu/paper-menu.html">
<link rel="import" href="/bower_components/paper-item/paper-item.html">
<link rel="import" href="/bower_components/paper-item/paper-icon-item.html">

<!-- <link rel="import" href="/elements/first-element.html" /> -->
{{-- Behaviors --}}
<link rel="import" href="/grid-elements/behaviors.fold">
<link rel="import" href="/grid-elements/behaviors.tabs">
<link rel="import" href="/grid-elements/behaviors.map">


<!-- Custom Elements -->
<link rel="import" href="/grid-elements/grid-shared-styles">
<link rel="import" href="/grid-elements/grid-drawer">
<link rel="import" href="/grid-elements/grid-view">

<!-- second fold -->
<link rel="import" href="/grid-elements/grid-second-fold">
<link rel="import" href="/grid-elements/1.grid-auth">
<link rel="import" href="/grid-elements/1.grid-inbox">
<link rel="import" href="/grid-elements/1.grid-jobs">
<link rel="import" href="/grid-elements/1.grid-transactions">

<!-- third fold -->
<link rel="import" href="/grid-elements/grid-third-fold">
@if (Auth::guest())
                           
@else
    {{ Auth::user()->name }}
@endif