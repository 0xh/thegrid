<dom-module id="grid-jobs">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host, paper-scroll-header-panel {
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
		paper-progress {
			width: 100%;
		}
	</style>
	<template>
		<paper-scroll-header-panel class="flex">
		    <paper-toolbar slot="header">
		      <div class="flex">Jobs</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="listbox">
		    	<template is="dom-if" if="[[isLoading]]">
		    		<paper-progress indeterminate></paper-progress>
		    	</template>
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
				<template is="dom-if" if="[[pagination.next_page_url]]">
					<paper-item on-tap="loadJobs">
						<paper-item-body>
							<div >Load more</div>
						</paper-item-body>
					</paper-item>
				</template>
			</div>
		 </paper-scroll-header-panel>
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
				},
				pagination: {
					type: Object,
					value: function() {
						return [];
					},
					notify: true
				}
			},
			//observers: ['_jobChange(jobs)'],
			listeners: {
			"content-scroll": "scroll"
			},
			scroll: function(e, d){
				if (d.target.scrollHeight - (d.target.clientHeight * 1.5) < d.target.scrollTop) {
        			//console.log(d.target.scrollHeight, d.target.clientHeight, d.target.scrollTop);
        			if(!this.isLoading) {
        				this.loadJobs();
        			}
   				}
   			},
			behaviors: [
				GridBehaviors.FoldBehavior
			],
			_jobChange: function(jobs) {
				this.jobs = jobs;
			},
			viewJob: function(e) {
				var job = e.model.job;
				this.thirdFold.component = 'job'
				// this.thirdFold.$.job.job = job;
				this.thirdFold.$.job.id = job.id;
				this.thirdFold.open();
				var jobs = this.jobs;
				this.indexOpened = jobs.findIndex(x => x.id == job.id);
			},
			close: function() {
				this.secondFold.close();
			},
			insertJob: function(job) {
				this.unshift('jobs', job);
			},
			loadJobs: function() {
				var self = this,
					pagination = self.pagination;
				if(pagination.next_page_url) {
					self.isLoading = true;
					axios.get(pagination.next_page_url)
						.then(function (response) {
							var data = response.data,
								c_j = self.jobs;
							self.jobs = c_j.concat(data.data);
							self.pagination = {
								total: data.total,
								per_page: data.per_page,
								current_page: data.current_page,
								last_page: data.last_page,
								next_page_url: data.next_page_url,
								prev_page_url: data.prev_page_url,
								from: data.from,
								to: data.to
							};
							self.isLoading = false;
						});
				}
			},
			refreshJobs: function() {
				var self = this;
				axios.get('/'+Grid.user_id+'/jobs')
					.then(function (response) {
						var data = response.data;
						self.jobs = data.data;
						self.pagination = {
							total: data.total,
							per_page: data.per_page,
							current_page: data.current_page,
							last_page: data.last_page,
							next_page_url: data.next_page_url,
							prev_page_url: data.prev_page_url,
							from: data.from,
							to: data.to
						};
						self.isLoading = false;
					});
			},
			init: function() {
				var self = this;
				self.refreshJobs();
				socket.on('add-bid', function(data) {
					var jobs = self.jobs,
						index = jobs.findIndex(x => x.id == data.job_id);
					if(self.jobs[index]) {
						self.unshift('jobs.' + index + '.bids', data);
						self.notifyPath('jobs.' + index + '.bids');
					}
					if(self.indexOpened > -1) {
						if(index == self.indexOpened) {
							self.thirdFold.$.job.refreshJob();
						}
					}
				});
				this.isInit = true;
			}
		});
	}());
</script>
