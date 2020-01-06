

module.exports = function (nga, admin) {
    var apiUsers = admin.getEntity("api_users");

    apiUsers.listView().fields([
        nga.field("id"),
        nga.field("apiKey"),
        nga.field("company"),
        nga.field("application"),
        nga.field("email")
    ]).title("Api Users List")
        .perPage(10)
        .listActions([
            //"<toggle-api-user size=\"xs\" api-user=\"::entry\"></toggle-api-user>",
           "show","edit"])
        .filters([
            nga.field("apiKey"),
            nga.field("company"),
    ]);

    apiUsers.showView().fields([
        nga.field("id"),
        nga.field("apiKey"),
        nga.field("company"),
        nga.field("application"),
        nga.field("email"),
        nga.field("isActive"),
        nga.field("created_at"),
        nga.field("updated_at"),
        nga.field("events", "referenced_list") // display list of related events
            .targetEntity(nga.entity("events"))
            .targetReferenceField("apiUser")
            .targetFields([
                nga.field("id").isDetailLink(true),
                nga.field("object"),
                nga.field("action"),
                nga.field("version"),
                nga.field("created_at"),
            ])
            .sortField("created_at")
            .sortDir("desc")
            .listActions(["show","edit"])
            .label("Latest Events"),
        nga.field("","template")
            .label("")
            .template("<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" size=\"sm\"></ma-filtered-list-button>")
    ]).title("Api User")
        .actions([
            "<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" label=\"Events\"></ma-filtered-list-button>",
            "<toggle-api-user api-user=\"::entry\"></toggle-api-user>",
            "list"
        ]);

    apiUsers.creationView().fields([
        nga.field("apiKey").validation({ required: true, minlength: 3, maxlength: 255 }),
        nga.field("company").validation({ required: true, minlength: 3, maxlength: 255 }),
        nga.field("application").validation({ required: true, minlength: 3, maxlength: 255 }),
        nga.field("email","email").validation({ required: true, maxlength: 255 }),
    ]).title("Api User")
        .actions(["list"]);

    apiUsers.editionView()
        .fields([
            nga.field("apiKey").validation({ required: true, minlength: 3, maxlength: 255 }),
            nga.field("company").validation({ required: true, minlength: 3, maxlength: 255 }),
            nga.field("application").validation({ required: true, minlength: 3, maxlength: 255 }),
            nga.field("email","email").validation({ required: true, maxlength: 255 }),
            nga.field("isActive").editable(false),
            nga.field("events", "referenced_list") // display list of related events
            .targetEntity(nga.entity("events"))
            .targetReferenceField("apiUser")
            .targetFields([
                nga.field("id").isDetailLink(true),
                nga.field("object"),
                nga.field("action"),
                nga.field("version"),
                nga.field("created_at"),
            ])
            .sortField("created_at")
            .sortDir("desc")
            .listActions(["show","edit"])
            .label("Latest Events"),
        nga.field("","template")
            .label("")
            .template("<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" size=\"sm\"></ma-filtered-list-button>")
        ])
        .title("Api User")
        .actions([
            "<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" label=\"Events\"></ma-filtered-list-button>",
            "<toggle-api-user api-user=\"::entry\"></toggle-api-user>",
            "list",
        ]);

    apiUsers.editionView().onSubmitError(["error","form","progression","notification",
        function(error, form, progression, notification){
            // stop the progress bar
            progression.done();
            // add a notification
            notification.log("error "+ error.data.code+": "+error.data.message, { addnCls: "humane-flatty-error" });
            // cancel the default action (default error messages)
            return false;
    }]);

    apiUsers.creationView().onSubmitError(apiUsers.editionView().onSubmitError());

    return apiUsers;
};