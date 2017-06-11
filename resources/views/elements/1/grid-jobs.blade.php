<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/grid-elements/scripts.axios">

<dom-module id="grid-jobs">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
		.icon-badge {
			float: right;
		    color: #ffffff;
		    background: red;
		    border-radius: 50%;
		    width: 24px;
		    height: 24px;
		    text-align: center;
		    font-style: normal;
		}
	</style>
	<template>
		<paper-header-panel class="flex">
		    <paper-toolbar slot="header">
		      <div class="flex">Jobs</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="listbox">
			 	<template is="dom-repeat" items="@{{jobs}}" as="job">
					<paper-item on-tap="viewJob" data-id$=@{{job.id}}>					
						<paper-item-body two-line>
							<div>@{{job.name}}</div>
							<div secondary>@{{job.location}}</div>					
						</paper-item-body>
						<i class="icon-badge">@{{job.bids.length}}</i>
						<paper-ripple></paper-ripple>
					</paper-item>
				</template>
			</div>
		 </paper-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-jobs',
			properties: {
				jobs: {
					type: Object,
					value: function() {
						return [];
					},
					notify: true,
				},
				indexOpened: {
					type: Number,
					value: -1,
					notify: true
				}
			},
			//observers: ['_jobChange(jobs)'],
			behaviors: [
				GridBehaviors.FoldBehavior
			],
			_jobChange: function(jobs) {
				console.log('main job has changed');
				this.jobs = jobs;
			},
			viewJob: function(e) {
				var job = e.model.job;
				this.thirdFold.component = 'job'
				this.thirdFold.$.job.job = job;
				this.thirdFold.open();
				var jobs = this.jobs;
				this.indexOpened = jobs.findIndex(x => x.id == job.id);
				console.log('selected index ', self.indexOpened);
			},
			close: function() {
				this.secondFold.close();
			},
			insertJob: function(job) {
				this.unshift('jobs', job);
			},
			refreshJobs: function() {
				var self = this;
				axios.get('/'+Grid.user_id+'/jobs')
					.then(function (response) {
						var data = response.data;
						self.jobs = data;
						console.log('jobs', data);
					});
			},
			attached: function() {
				var self = this;
				this.refreshJobs();
				socket.on('add-bid', function(data) {
					var jobs = self.jobs;
					var index = jobs.findIndex(x => x.id == data.job_id);
					console.log('index', index);
					self.unshift('jobs.' + index + '.bids', data);
					self.notifyPath('jobs.' + index + '.bids');
					// self.notifyPath('jobs.' + index + '.bids.legth');
					// self.thirdFold.$.job.job. = self.jobs[index];
					console.log('selected index', self.indexOpened);
					if(self.indexOpened > -1) {
						if(index == self.indexOpened) {
							console.log('updating the view of current open job');
							console.log('3rdfold', self.thirdFold.$.job);
							self.thirdFold.$.job.refreshJob(self.jobs[index]);
							self.thirdFold.$.job.notifyPath('job.bids');
						} else {
							console.log('the job is not opened yet');
						}
					} else {
						console.log('no selected index');
					}
					console.log('main job add-bid socket');
				});
			}
		});
	}());
</script>
