<dom-module id="grid-confirmation">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
		.container {
			margin: 0 20px;
		}
		.main-price {
			font-weight: 400;
			font-size: 18px;
		}
	</style>
	<template>
		<paper-header-panel class="flex">
			<paper-toolbar slot="header">
				<div class="flex">Confirmation</div>
				<paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
			</paper-toolbar>
			<div role="">
				<div class="container">
					<p>Please enter the confirmation code sent to your phone</p>
					<p>Did not receive text? 
						<template is="dom-if" if="[[isResending]]">
							<paper-spinner id="spinner" active></paper-spinner>
						</template>
						<template is="dom-if" if="[[!isResending]]">
							<a href="#" on-tap="resendCode">Resend</a>
						</template>
					</p>
					<template is="dom-if" if="[[isCodeResend]]">
						<p>[[codeResendMessage]]</p>
					</template>
					<paper-input label="Confirmation Code" id="code" name="code" value="@{{code}}" error-message="@{{errorCode}}"></paper-input>
					<paper-button raised on-tap="submitCode">
						<template is="dom-if" if="[[isLoading]]">
							<paper-spinner id="spinner" active></paper-spinner>
						</template>
						<template is="dom-if" if="[[!isLoading]]">
							<span>Submit</span>
						</template>
					</paper-button>
				</div>
			</div>
		</paper-header-panel>
	</template>
	<script>
		Polymer({
			is: 'grid-confirmation',
			properties: {
				code: Number,
				isLoading: {
					type: Boolean,
					value: false
				},
				isResending: {
					type: Boolean,
					value: false
				},
				isCodeResend: {
					type: Boolean,
					value: false
				},
				codeResendMessage: String
			},
			observers: [],
			behaviors: [
			GridBehaviors.FoldBehavior
			],
			submitCode: function() {
				var self = this;
				self.isLoading = true;
				axios.post('/submitCode', {
					confirmation_code: self.code,
				}).then(function(response) {
					var data = response.data;
					// redirect to home after confirmation
					window.location.href = data.redirectUrl;

					self.$.code.invalid = false;
					self.isLoading = false;
				}).catch(function (error) {
					if (error.response) {
						var data = error.response.data;
						self.errorCode = data.message;
						self.$.code.invalid = true;
						self.isLoading = false;
					}
				});
			},
			resendCode: function() {
				var self = this;
				self.isResending = true;
				axios.post('/resendCode')
					.then(function(response) {
						var data = response.data;
						self.isCodeResend = true;
						self.codeResendMessage = data.message;
						self.isResending = false;
					})
					.catch(function(error) {
						if(error.response) {
							var data = error.response.data;
							self.isCodeResend = true;
							self.codeResendMessage = data.message;
							self.isResending = false;
						}
					});
			},
			close: function() {
				this.thirdFold.close();
			},
			ready: function() {
				
			}
		});
	</script>
</dom-module>