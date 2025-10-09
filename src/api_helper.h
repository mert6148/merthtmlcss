#ifndef API_HELPER_H
#define API_HELPER_H

#include "api.h"
#include <curl/curl.h>
#include <json/json.h>
#include <thread>
#include <mutex>
#include <condition_variable>
#include <queue>
#include <atomic>

namespace MerthtmlcssAPI
{

    // CURL callback structure
    struct CurlResponse
    {
        std::string data;
        std::map<std::string, std::string> headers;
        long statusCode;
    };

    // HTTP Helper sınıfı
    class HttpHelper
    {
    public:
        static HttpHelper &getInstance();

        // CURL operations
        static size_t WriteCallback(void *contents, size_t size, size_t nmemb, void *userp);
        static size_t HeaderCallback(void *contents, size_t size, size_t nmemb, void *userp);

        // Request execution
        static std::shared_ptr<Response> executeRequest(const Request &request);
        static std::shared_ptr<Response> executeRequestWithCurl(const Request &request);

        // URL utilities
        static std::string buildFullUrl(const std::string &baseUrl, const std::string &endpoint);
        static std::string encodeUrl(const std::string &url);
        static std::string decodeUrl(const std::string &url);

        // Header utilities
        static std::string formatHeaders(const std::map<std::string, std::string> &headers);
        static std::map<std::string, std::string> parseHeaders(const std::string &headerString);

        // JSON utilities
        static std::string toJson(const std::map<std::string, std::string> &data);
        static std::map<std::string, std::string> fromJson(const std::string &json);
        static bool isValidJson(const std::string &json);

        // Error handling
        static std::string getCurlErrorString(CURLcode code);
        static bool isNetworkError(int statusCode);
        static bool isClientError(int statusCode);
        static bool isServerError(int statusCode);

        // Timeout utilities
        static void setCurlTimeout(CURL *curl, int timeoutMs);
        static void setCurlOptions(CURL *curl, const Request &request);

        // Authentication utilities
        static void setCurlAuth(CURL *curl, const std::string &token);
        static void setCurlBasicAuth(CURL *curl, const std::string &username, const std::string &password);

    private:
        HttpHelper() = default;
        static std::mutex curlMutex_;
        static bool curlInitialized_;
    };

    // Request Builder sınıfı
    class RequestBuilder
    {
    public:
        RequestBuilder();
        RequestBuilder(const std::string &baseUrl);

        // Fluent interface methods
        RequestBuilder &url(const std::string &url);
        RequestBuilder &method(HttpMethod method);
        RequestBuilder &header(const std::string &key, const std::string &value);
        RequestBuilder &body(const std::string &body);
        RequestBuilder &queryParam(const std::string &key, const std::string &value);
        RequestBuilder &json(const std::map<std::string, std::string> &data);
        RequestBuilder &formData(const std::map<std::string, std::string> &data);
        RequestBuilder &timeout(int timeoutMs);
        RequestBuilder &auth(const std::string &token);
        RequestBuilder &basicAuth(const std::string &username, const std::string &password);

        // Build methods
        Request build();
        std::shared_ptr<Request> buildShared();

        // Reset
        RequestBuilder &reset();

    private:
        std::string baseUrl_;
        std::string url_;
        HttpMethod method_;
        std::map<std::string, std::string> headers_;
        std::string body_;
        std::map<std::string, std::string> queryParams_;
        int timeoutMs_;
        std::string authToken_;
        std::string basicAuth_;
    };

    // Response Parser sınıfı
    class ResponseParser
    {
    public:
        static ResponseParser &getInstance();

        // Parse methods
        static std::map<std::string, std::string> parseJson(const std::string &json);
        static std::vector<std::map<std::string, std::string>> parseJsonArray(const std::string &json);
        static std::map<std::string, std::string> parseXml(const std::string &xml);
        static std::map<std::string, std::string> parseFormData(const std::string &formData);

        // Validation methods
        static bool isValidResponse(const Response &response);
        static bool isJsonResponse(const Response &response);
        static bool isXmlResponse(const Response &response);

        // Extraction methods
        static std::string extractString(const std::map<std::string, std::string> &data, const std::string &key);
        static int extractInt(const std::map<std::string, std::string> &data, const std::string &key);
        static bool extractBool(const std::map<std::string, std::string> &data, const std::string &key);
        static std::vector<std::string> extractArray(const std::map<std::string, std::string> &data, const std::string &key);

    private:
        ResponseParser() = default;
    };

    // Async Request Manager sınıfı
    class AsyncRequestManager
    {
    public:
        AsyncRequestManager();
        ~AsyncRequestManager();

        // Async operations
        void sendAsync(const Request &request, std::function<void(std::shared_ptr<Response>)> callback);
        void sendAsync(const std::vector<Request> &requests, std::function<void(std::vector<std::shared_ptr<Response>>)> callback);

        // Batch operations
        void sendBatch(const std::vector<Request> &requests, std::function<void(std::vector<std::shared_ptr<Response>>)> callback);
        void sendParallel(const std::vector<Request> &requests, std::function<void(std::vector<std::shared_ptr<Response>>)> callback);

        // Control methods
        void stop();
        void waitForCompletion();
        bool isRunning() const;
        size_t getQueueSize() const;

        // Configuration
        void setMaxConcurrentRequests(size_t max);
        void setRetryAttempts(int attempts);
        void setRetryDelay(int delayMs);

    private:
        std::queue<std::pair<Request, std::function<void(std::shared_ptr<Response>)>>> requestQueue_;
        std::vector<std::thread> workers_;
        std::mutex queueMutex_;
        std::condition_variable queueCondition_;
        std::atomic<bool> running_;
        std::atomic<size_t> maxConcurrentRequests_;
        std::atomic<int> retryAttempts_;
        std::atomic<int> retryDelay_;

        void workerThread();
        std::shared_ptr<Response> executeWithRetry(const Request &request);
    };

    // Cache Manager sınıfı
    class CacheManager
    {
    public:
        static CacheManager &getInstance();

        // Cache operations
        void set(const std::string &key, const std::string &value, int ttlSeconds = 3600);
        std::string get(const std::string &key);
        void remove(const std::string &key);
        void clear();
        bool exists(const std::string &key);

        // Cache control
        void setMaxSize(size_t maxSize);
        void setDefaultTtl(int ttlSeconds);
        size_t getSize() const;
        std::vector<std::string> getKeys() const;

        // Cache statistics
        size_t getHitCount() const;
        size_t getMissCount() const;
        double getHitRate() const;

    private:
        struct CacheEntry
        {
            std::string value;
            std::chrono::time_point<std::chrono::steady_clock> expiry;
        };

        std::map<std::string, CacheEntry> cache_;
        std::mutex cacheMutex_;
        size_t maxSize_;
        int defaultTtl_;
        std::atomic<size_t> hitCount_;
        std::atomic<size_t> missCount_;

        CacheManager();
        void cleanup();
        bool isExpired(const CacheEntry &entry) const;
    };

    // Rate Limiter sınıfı
    class RateLimiter
    {
    public:
        RateLimiter(int maxRequests, int windowSeconds);

        // Rate limiting
        bool allowRequest();
        bool allowRequest(const std::string &key);
        void reset();
        void reset(const std::string &key);

        // Statistics
        int getRemainingRequests() const;
        int getRemainingRequests(const std::string &key) const;
        int getResetTime() const;
        int getResetTime(const std::string &key) const;

    private:
        struct RateLimitEntry
        {
            int count;
            std::chrono::time_point<std::chrono::steady_clock> windowStart;
        };

        int maxRequests_;
        int windowSeconds_;
        std::map<std::string, RateLimitEntry> limits_;
        std::mutex limitsMutex_;

        bool isWindowExpired(const RateLimitEntry &entry) const;
    };

    // Logger sınıfı
    class Logger
    {
    public:
        enum class Level
        {
            DEBUG,
            INFO,
            WARNING,
            ERROR
        };

        static Logger &getInstance();

        // Logging methods
        void log(Level level, const std::string &message);
        void debug(const std::string &message);
        void info(const std::string &message);
        void warning(const std::string &message);
        void error(const std::string &message);

        // Configuration
        void setLevel(Level level);
        void setOutput(std::ostream &output);
        void enableTimestamp(bool enable);
        void enableThreadId(bool enable);

    private:
        Level currentLevel_;
        std::ostream *output_;
        bool enableTimestamp_;
        bool enableThreadId_;
        std::mutex logMutex_;

        Logger();
        std::string formatMessage(Level level, const std::string &message);
        std::string getTimestamp();
        std::string getThreadId();
    };

} // namespace MerthtmlcssAPI

#endif // API_HELPER_H
