<style is="custom-style">
	:root {
		--grid-drawer-collapse-width: 56px; /* 72px */
		--grid-drawer-expanded-width: 253px; /* 333px */
		--grid-second-fold-width: 400px; /* 519px */
		--grid-third-fold-width-comp: calc(100% - (var(--grid-drawer-collapse-width) + var(--grid-second-fold-width)));
		--grid-third-fold-width: calc(100% - 456px); /*var(--grid-third-fold-width-comp);*/
		--grid-third-fold-min-width: 400px;
		--grid-transition-effect: {
			transition-property: left;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
			transition-duration: 350ms;
		}

		--paper-material-padding: 12px 8px;
		--grid-border-color: #d6d6d6;
	}
</style>
<dom-module id="grid">
	<template>
		<style>
			.container {
				margin: 0 20px;
			}
			.h100 {
				height: 100%;
			}
			.w100 {
				width: 100%;
			}
			.border-bottom {
				border-bottom: 1px solid var(--grid-border-color);
			}
			.border-top {
				border-top: 1px solid var(--grid-border-color);
			}
		</style>
	</template>
</dom-module>