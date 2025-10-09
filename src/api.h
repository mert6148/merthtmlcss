#ifndef API_H
#define API_H

#include <iostream>
#include <string>
#include <vector>
#include <map>
#include <memory>
#include <functional>

namespace MerthtmlcssAPI
{

    // HTTP Method enum
    enum class HttpMethod
    {
        GET,
        POST,
        PUT,
        DELETE,
        PATCH,
        HEAD,
        OPTIONS
    };

    // HTTP Status Codes
    enum class HttpStatus
    {
        OK = 200,
        CREATED = 201,
        NO_CONTENT = 204,
        BAD_REQUEST = 400,
        UNAUTHORIZED = 401,
        FORBIDDEN = 403,
        NOT_FOUND = 404,
        METHOD_NOT_ALLOWED = 405,
        INTERNAL_SERVER_ERROR = 500,
        NOT_IMPLEMENTED = 501,
        BAD_GATEWAY = 502,
        SERVICE_UNAVAILABLE = 503
    };

    // Request sınıfı
    class Request
    {
    public:
        Request();
        Request(const std::string &url, HttpMethod method = HttpMethod::GET);
        virtual ~Request() = default;

        // URL ve method ayarları
        void setUrl(const std::string &url);
        void setMethod(HttpMethod method);
        void setHeader(const std::string &key, const std::string &value);
        void setBody(const std::string &body);
        void setQueryParam(const std::string &key, const std::string &value);

        // Getter'lar
        const std::string &getUrl() const;
        HttpMethod getMethod() const;
        const std::map<std::string, std::string> &getHeaders() const;
        const std::string &getBody() const;
        const std::map<std::string, std::string> &getQueryParams() const;

        // Utility fonksiyonlar
        std::string getMethodString() const;
        std::string getFullUrl() const;
        void clear();

    private:
        std::string url_;
        HttpMethod method_;
        std::map<std::string, std::string> headers_;
        std::string body_;
        std::map<std::string, std::string> queryParams_;
    };

    // Response sınıfı
    class Response
    {
    public:
        Response();
        Response(HttpStatus status, const std::string &body = "");
        virtual ~Response() = default;

        // Status ve body ayarları
        void setStatus(HttpStatus status);
        void setBody(const std::string &body);
        void setHeader(const std::string &key, const std::string &value);

        // Getter'lar
        HttpStatus getStatus() const;
        const std::string &getBody() const;
        const std::map<std::string, std::string> &getHeaders() const;
        int getStatusCode() const;
        bool isSuccess() const;

        // Utility fonksiyonlar
        std::string getStatusText() const;
        void clear();

    private:
        HttpStatus status_;
        std::string body_;
        std::map<std::string, std::string> headers_;
    };

    // API Client sınıfı
    class ApiClient
    {
    public:
        ApiClient();
        ApiClient(const std::string &baseUrl);
        virtual ~ApiClient() = default;

        // Base URL ayarları
        void setBaseUrl(const std::string &baseUrl);
        const std::string &getBaseUrl() const;

        // HTTP istekleri
        std::shared_ptr<Response> get(const std::string &endpoint);
        std::shared_ptr<Response> post(const std::string &endpoint, const std::string &body = "");
        std::shared_ptr<Response> put(const std::string &endpoint, const std::string &body = "");
        std::shared_ptr<Response> del(const std::string &endpoint);
        std::shared_ptr<Response> patch(const std::string &endpoint, const std::string &body = "");

        // Generic request metodu
        std::shared_ptr<Response> sendRequest(const Request &request);

        // Timeout ayarları
        void setTimeout(int timeoutMs);
        int getTimeout() const;

        // Authentication
        void setAuthToken(const std::string &token);
        void setBasicAuth(const std::string &username, const std::string &password);

        // Event callbacks
        void setOnRequest(std::function<void(const Request &)> callback);
        void setOnResponse(std::function<void(const Response &)> callback);
        void setOnError(std::function<void(const std::string &)> callback);

    private:
        std::string baseUrl_;
        int timeoutMs_;
        std::string authToken_;
        std::string basicAuth_;

        // Event callbacks
        std::function<void(const Request &)> onRequest_;
        std::function<void(const Response &)> onResponse_;
        std::function<void(const std::string &)> onError_;

        // Internal helper methods
        std::string buildUrl(const std::string &endpoint) const;
        void addDefaultHeaders(Request &request) const;
        std::shared_ptr<Response> executeRequest(const Request &request) const;
    };

    // API Controller sınıfı
    class ApiController
    {
    public:
        ApiController();
        ApiController(const std::string &baseUrl);
        virtual ~ApiController() = default;

        // System endpoints
        std::shared_ptr<Response> getSystemInfo();
        std::shared_ptr<Response> getSystemRequirements();
        std::shared_ptr<Response> checkSystem();
        std::shared_ptr<Response> getInstallCommands();
        std::shared_ptr<Response> healthCheck();

        // Custom endpoints
        std::shared_ptr<Response> customRequest(const std::string &endpoint, HttpMethod method, const std::string &body = "");

        // Batch operations
        std::vector<std::shared_ptr<Response>> batchRequest(const std::vector<Request> &requests);

        // Error handling
        void setErrorHandler(std::function<void(const std::string &, int)> handler);
        void handleError(const std::string &message, int code = 500);

    private:
        std::unique_ptr<ApiClient> client_;
        std::function<void(const std::string &, int)> errorHandler_;

        // Internal helper methods
        void initializeClient();
        void setupDefaultHeaders();
    };

    // Utility fonksiyonlar
    namespace Utils
    {
        std::string methodToString(HttpMethod method);
        HttpMethod stringToMethod(const std::string &method);
        std::string statusToString(HttpStatus status);
        std::string urlEncode(const std::string &str);
        std::string urlDecode(const std::string &str);
        std::string buildQueryString(const std::map<std::string, std::string> &params);
        std::map<std::string, std::string> parseQueryString(const std::string &query);
        bool isValidUrl(const std::string &url);
        std::string formatJson(const std::string &json);
    }

    // Exception sınıfları
    class ApiException : public std::exception
    {
    public:
        ApiException(const std::string &message);
        const char *what() const noexcept override;

    private:
        std::string message_;
    };

    class NetworkException : public ApiException
    {
    public:
        NetworkException(const std::string &message);
    };

    class TimeoutException : public ApiException
    {
    public:
        TimeoutException(const std::string &message);
    };

    class HttpException : public ApiException
    {
    public:
        HttpException(const std::string &message, int statusCode);
        int getStatusCode() const;

    private:
        int statusCode_;
    };

} // namespace MerthtmlcssAPI

#endif // API_H
