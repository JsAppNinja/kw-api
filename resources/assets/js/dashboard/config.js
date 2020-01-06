
module.exports = function (nga, admin) {
    return nga.dashboard()
        .addCollection(nga.collection(admin.getEntity("api_users"))
            .name("latest_api_users")
            .title("Latest ApiUsers")
            .fields([
                nga.field("id"),
                nga.field("apiKey"),
                nga.field("company"),
            ])
            .sortField("id")
            .sortDir("desc")
            .perPage(5) 
        )
        .addCollection(nga.collection(admin.getEntity("events"))
            .name("latest_events")
            .title("Latest Events")
            .fields([
                nga.field("id"),
                nga.field("object"),
                nga.field("action"),
                nga.field("version")
            ])
            .sortField("id")
            .sortDir("desc")
            .perPage(5) 
        )
        .addCollection(nga.collection(admin.getEntity("subscribers"))
            .name("latest_subscribers")
            .title("Latest Subscribers")
            .fields([
                nga.field("id"),
                nga.field("event_id","reference")
                    .targetEntity(admin.getEntity("events"))
                    .targetField(nga.field("id"))
                    .label("Event"),
                nga.field("api_user_id","reference")
                    .targetEntity(admin.getEntity("api_users"))
                    .targetField(nga.field("company"))
                    .label("Api User Company"),
            ])
            .sortField("id")
            .sortDir("desc")
            .perPage(5) 
        )
        .template(`
<div class="row dashboard-starter"></div>
<dashboard-summary></dashboard-summary>
<div class="row dashboard-content">
    <div class="col-lg-6">
        <div class="panel panel-green">
            <ma-dashboard-panel collection="dashboardController.collections.latest_api_users" entries="dashboardController.entries.latest_api_users" datastore="dashboardController.datastore"></ma-dashboard-panel>
        </div>
        <div class="panel panel-yellow">
            <ma-dashboard-panel collection="dashboardController.collections.latest_events" entries="dashboardController.entries.latest_events" datastore="dashboardController.datastore"></ma-dashboard-panel>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <ma-dashboard-panel collection="dashboardController.collections.latest_subscribers" entries="dashboardController.entries.latest_subscribers" datastore="dashboardController.datastore"></ma-dashboard-panel>
        </div>
    </div>
</div>
`);
};