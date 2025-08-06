// Ortak veritabanı yardımcı fonksiyonları
object DBUtils {
  def openConnection(): Unit = {
    println("Veritabanı bağlantısı açıldı.")
  }
  def closeConnection(): Unit = {
    println("Veritabanı bağlantısı kapatıldı.")
  }
  def logError(error: String): Unit = {
    println(s"[DB_ERROR] $error")
  }
}
