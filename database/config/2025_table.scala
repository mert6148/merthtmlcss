// Database yapılandırması ve bağlantı fonksiyonları
object DatabaseConfig {
  val dbConfig: Map[String, Any] = Map(
    "host" -> "localhost",
    "port" -> 5432,
    "user" -> "dbuser",
    "password" -> "dbpassword",
    "database" -> "mydatabase"
  )

  // Bağlantı dizesi oluşturucu
  def getConnectionString: String = {
    s"jdbc:postgresql://${dbConfig("host")}:${dbConfig("port")}/${dbConfig("database")}?user=${dbConfig("user")}&password=${dbConfig("password")}" 
  }

  // Yapılandırma doğrulama
  def validateConfig: Boolean = {
    dbConfig.contains("host") && dbConfig.contains("port") &&
    dbConfig.contains("user") && dbConfig.contains("password") &&
    dbConfig.contains("database")
  }
}

object Table2025 {
  // Bağlantı dizesini ekrana yazdıran fonksiyon
  def printConnectionString(): Unit = {
    if (DatabaseConfig.validateConfig) {
      println(s"Database Connection String: ${DatabaseConfig.getConnectionString}")
    } else {
      println("Geçersiz veritabanı yapılandırması.")
    }
  }

  def main(args: Array[String]): Unit = {
    printConnectionString()
  }
}