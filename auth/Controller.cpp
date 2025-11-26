#include <iostream>
#include <string>

class API {
public:
    API(const std::string& name, const std::string& email, const std::string& message)
        : name_(name), email_(email), message_(message) {}
    std::string getName() const { return name_; }
    std::string getEmail() const { return email_; }
    std::string getMessage() const { return message_; }
private:
    std::string name_;
    std::string email_;
    std::string message_;
};

class Controller
{
    public:
        Controller();
        ~Controller();
        void printInfo() const;
    private:
        API api_;
};

Controller::Controller()
    : api_("Mert Doğanay", "mertdoganay437@gmail.com", "Merhaba, Merthtmlcss projesi harika!")
{
}

void Controller::printInfo() const {
    std::cout << "Name: " << api_.getName() << std::endl;
    std::cout << "Email: " << api_.getEmail() << std::endl;
    std::cout << "Message: " << api_.getMessage() << std::endl;
}