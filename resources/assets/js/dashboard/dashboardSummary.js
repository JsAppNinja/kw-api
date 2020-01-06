
var hasSeenAlert = false;
var dashboardSummaryTemplate = `
<div class="row">
    <div class="col-lg-12">
        <uib-alert type="info" close="dismissAlert()" ng-show="!hasSeenAlert">
            Welcome to the KW-API.
        </uib-alert>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{mqStats.queue_totals.messages}}</div>
                        <div>Queue, {{mqStats.queue_totals.messages_details.rate}}/s rate.</div>
                    </div>
                </div>
            </div>
            <a href="#/stats/queues">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>

        </div>
    </div>
    <div class="col-lg-3">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-download fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{mqStats.message_stats.deliver_get_details.rate}}/s</div>
                        <div>Deliver rate</div>
                    </div>
                </div>
            </div>
            <a href="#/stats/queues">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-bullhorn fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{eventStats.totalCount}}</div>
                        <div>Number of Events</div>
                    </div>
                </div>
            </div>
            <a ui-sref="list({entity:'events'})">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-bell fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">124</div>
                        <div>Add Event Calls</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
`;

function dashboardSummary(Restangular) {
    "use strict";

    return {
        restrict: "E",
        scope: {},
        controller ($scope) {
            $scope.mqStats = {};
            $scope.eventStats = {};
            $scope.hasSeenAlert = hasSeenAlert;
            $scope.dismissAlert = () => {
                hasSeenAlert = true;
                $scope.hasSeenAlert = true;
            };
            Restangular.one("mq","overview").get()
                .then(function(response){
                    $scope.mqStats = response.data.plain();
                });
            Restangular.all("events").getList()
                .then(function(response){
                    $scope.eventStats.totalCount = response.totalCount;
                });
        },
        template: dashboardSummaryTemplate
    };
}

dashboardSummary.$inject = ["Restangular"];

exports["default"] = dashboardSummary;
module.exports = exports["default"];
