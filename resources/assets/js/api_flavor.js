function requestInterceptor(RestangularProvider) {
    // use the custom query parameters function to format the API request correctly
    RestangularProvider.addFullRequestInterceptor(function(element, operation, what, url, headers, params, httpConfig) {
        if (operation === "getList") {
            params.page = params._page; // change page variable
            delete params._page;
        }
        headers["apiKey"] = window.localStorage.getItem("kw_api_key_store");
        return { params, headers };
    });
}

function responseInterceptor(RestangularProvider) {
    RestangularProvider.addResponseInterceptor(function(data,operation,what,url,response,deffered){
        var extractedData = data;
        if (operation === "getList") {
            extractedData = data.data;
            response.totalCount = data.total; //set totalCount manually
        }
        return extractedData;
    });
}

module.exports = { requestInterceptor, responseInterceptor }