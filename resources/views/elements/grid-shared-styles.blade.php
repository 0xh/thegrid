<style is="custom-style">
:root {
	--grid-drawer-collapse-width: 56px;
	--grid-drawer-expanded-width: 250px;
	--grid-second-fold-width: 400px;
	--grid-third-fold-width-comp: calc(100% - (var(--grid-drawer-collapse-width) + var(--grid-second-fold-width)));
	--grid-third-fold-width: calc(100% - 456px); /*var(--grid-third-fold-width-comp);*/
	--grid-third-fold-min-width: 400px;
	--grid-transition-effect: {
		transition-property: left;
		transform-style: cubic-bezier(0.4, 0, 0.2, 1);
		transition-duration: 350ms;
	}

	--paper-material-padding: 12px 8px;
}
</style>