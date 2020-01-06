

module.exports = function (nga, admin) {
    var events = admin.getEntity("events");

    events.listView().fields([
        nga.field("id"),
        nga.field("object"),
        nga.field("action"),
        nga.field("version"),
        nga.field("apiUser","reference")
            .targetEntity(admin.getEntity("api_users"))
            .targetField(nga.field("company"))
            .label("Api User Company"),
        nga.field("created_at"),
        nga.field("updated_at"),
    ]).perPage(10)
    .listActions([
        "show","edit"
    ])
    .filters([
        nga.field("object"),
        nga.field("action"),
        nga.field("version"),
        nga.field("apiUser"),
    ]);

    events.showView().fields([
        nga.field("id"),
        nga.field("apiUser"),
        nga.field("object"),
        nga.field("action"),
        nga.field("version"),
        nga.field("jsonSchema","text"),
        nga.field("created_at"),
        nga.field("updated_at"),
        nga.field("subscribers", "referenced_list") // display list of related events
            .targetEntity(nga.entity("subscribers"))
            .targetReferenceField("event_id")
            .targetFields([
                nga.field("id").isDetailLink(true),
                nga.field("api_user_id"),
                nga.field("endPoint"),
                nga.field("created_at"),
            ])
            .sortField("created_at")
            .sortDir("desc")
            .listActions(["show","edit"])
            .label("Latest Subscribers"),
        nga.field("","template")
            .label("")
            .template("<ma-filtered-list-button entity-name=\"subscribers\" filter=\"{ event_id: entry.values.id }\"></ma-filtered-list-button>")
        ]).actions([
        "<ma-filtered-list-button entity-name=\"subscribers\" filter=\"{ event_id: entry.values.id }\" label=\"Subscribers\"></ma-filtered-list-button>",
        "<notify-subscriber event=\"::entry\"></notify-subscriber>",
        "list"
    ]);

    events.editionView().fields([
        nga.field("object")
            .validation({ required: true, maxlength: 255 }),
        nga.field("action")
            .validation({ required: true, maxlength: 255 }),
        nga.field("version")
            .validation({ required: true }),
        nga.field("jsonSchema","text").validation({ required: true }),
        nga.field("subscribers", "referenced_list") // display list of related events
            .targetEntity(nga.entity("subscribers"))
            .targetReferenceField("event_id")
            .targetFields([
                nga.field("id").isDetailLink(true),
                nga.field("api_user_id"),
                nga.field("endPoint"),
                nga.field("created_at"),
            ])
            .sortField("created_at")
            .sortDir("desc")
            .listActions(["show","edit"])
            .label("Latest Subscribers"),
        nga.field("","template")
            .label("")
            .template("<ma-filtered-list-button entity-name=\"subscribers\" size=\"sm\" filter=\"{ event_id: entry.values.id }\"></ma-filtered-list-button>")
    ]).actions([
        "<ma-filtered-list-button entity-name=\"subscribers\" filter=\"{ event_id: entry.values.id }\" label=\"Subscribers\"></ma-filtered-list-button>",
        "<notify-subscriber event=\"::entry\"></notify-subscriber>",
        "list"
    ]);

    events.editionView().onSubmitError(["error","form","progression","notification",
        function(error, form, progression, notification){
            // stop the progress bar
            progression.done();
            // add a notification
            notification.log("error "+ error.data.code+": "+error.data.message, { addnCls: "humane-flatty-error" });
            // cancel the default action (default error messages)
            return false;
    }]);

    return events; 
};