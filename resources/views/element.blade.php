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
<!-- <link rel="import" href="/grid-elements/grid-view"> -->
<link rel="import" href="/grid-elements/custom.grid-mobile-header">

<!-- second fold -->
<!-- <link rel="import" href="/grid-elements/grid-second-fold"> -->

<!-- third fold -->
<!-- <link rel="import" href="/grid-elements/grid-third-fold"> -->

<!-- scripts -->
<link rel="import" href="/grid-elements/scripts.axios">
<link rel="import" href="/grid-elements/scripts.socket-io">
@if (Auth::guest())

@else
    {{ Auth::user()->name }}
@endif
