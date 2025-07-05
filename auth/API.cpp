#include <string>

class API
{
    public:
        API(const std::string& name, const std::string& email, const std::string& message)
            : name_(name), email_(email), message_(message) {}
        ~API() = default;

        std::string getName() const { return name_; }
        std::string getEmail() const { return email_; }
        std::string getMessage() const { return message_; }

    private:
        std::string name_;
        std::string email_;
        std::string message_;
};

// Örnek kullanım
auto api = API("Mert Doğanay", "mertdoganay437@gmail.com", "Merhaba, Merthtmlcss projesi harika!");