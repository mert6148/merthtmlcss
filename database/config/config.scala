// Ortak veritabanı yapılandırması ve yardımcı fonksiyonlar
object DatabaseConfig {
  val dbConfig: Map[String, Any] = Map(
    "host" -> "localhost",
    "port" -> 5432,
    "user" -> "dbuser",
    "password" -> "dbpassword",
    "database" -> "mydatabase"
  )

  def getConnectionString: String = {
    s"jdbc:postgresql://${dbConfig("host")}:${dbConfig("port")}/${dbConfig("database")}?user=${dbConfig("user")}&password=${dbConfig("password")}" 
  }

  def validateConfig: Boolean = {
    dbConfig.contains("host") && dbConfig.contains("port") &&
    dbConfig.contains("user") && dbConfig.contains("password") &&
    dbConfig.contains("database")
  }
}

object Config {
  // Bağlantı dizesini ekrana yazdıran fonksiyon
  def printConnectionString(): Unit = {
    if (DatabaseConfig.validateConfig) {
      println(s"Database Connection String: ${DatabaseConfig.getConnectionString}")
    } else {
      println("Geçersiz veritabanı yapılandırması.")
    }
  }

  // Yapılandırmayı döndüren fonksiyon
  def getConfig: Map[String, Any] = DatabaseConfig.dbConfig

  def main(args: Array[String]): Unit = {
    printConnectionString()
  }
}