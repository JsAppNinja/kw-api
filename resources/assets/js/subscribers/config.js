

module.exports = function (nga, admin) {
    var subscribers = admin.getEntity("subscribers");

    subscribers.listView().fields([
        nga.field("id"),
        nga.field("event_id","reference")
            .targetEntity(admin.getEntity("events"))
            .targetField(nga.field("id"))
            .label("Event"),
        nga.field("api_user_id","reference")
            .targetEntity(admin.getEntity("api_users"))
            .targetField(nga.field("company"))
            .label("Api User Company"),
        nga.field("object"),
        nga.field("action"),
        nga.field("version"),
        nga.field("endPoint"),
        nga.field("created_at"),
    ]).perPage(10)
        .listActions([
            "show","edit"
        ])
        .filters([
            nga.field("object"),
            nga.field("action"),
            nga.field("version"),
            nga.field("event_id"),
            nga.field("api_user_id"),
        ]);

    subscribers.showView().fields([
        nga.field("id"),
        nga.field("event_id"),
        nga.field("api_user_id"),
        nga.field("object"),
        nga.field("action"),
        nga.field("version"),
        nga.field("endPoint"),
        nga.field("created_at"),
        nga.field("updated_at")
    ]);

    return subscribers; 
};