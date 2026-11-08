# ArenaFit – Spor Kompleksi WordPress Teması (v1.3.0)

## Güncelleme (mevcut siteye yükleme)
**Görünüm > Temalar > Yeni Ekle > Tema Yükle** ile yeni `arenafit.zip` dosyasını yükleyin, "Değiştir" deyin. İçeriğiniz (antrenör, paket, salon, rezervasyon) silinmez.
Yönetim paneli ilk açıldığında **Hesabım** ve **İletişim** sayfaları ile menüdeki **İletişim** bağlantısı kendiliğinden oluşturulur.
Sonra **Ayarlar > Kalıcı Bağlantılar > Değişiklikleri Kaydet** deyin (bir kez).

## 1.3.0 ile gelenler
- **Sesli okuma** (Erişilebilirlik paneli veya Alt+Shift+S; sayfadaki ilk Tab da "Sesli okumayı aç" düğmesine gelir): Tab ile ya da fareyle gelinen her bağlantı, düğme ve form alanı türü ve durumuyla Türkçe okunur. Seçilen tarih, uygun saatler, form hataları ve rezervasyon sonucu kendiliğinden okunur. Alt+Shift+R sayfayı okur, Alt+Shift+H başlıklar arasında gezer, Alt+Shift+K kısayolları okur, Esc susturur. Okuma hızı ayarlanabilir.
- **Sesli komut** (Chrome / Edge, mikrofon izni gerekir): "antrenörler", "paketler", "salonlar", "rezervasyon", "üyelik", "iletişim", "hesabım", "tesis", "sayfayı oku", "dur", "aşağı", "yukarı", "geri", "yardım"; "tıkla randevu al" ile düğmeye basar, "yaz Ayşe Demir" ile seçili alana yazar.
- Türkçe ses, ziyaretçinin cihazındaki seslerle çalışır (Windows/Edge, Android, iPhone'da hazır; yoksa panel uyarır). Ek eklenti veya ücretli servis gerekmez.

## 1.2.0 ile gelenler
- **Erişilebilirlik paneli** (sol alttaki düğme): yazı boyutu, yüksek kontrast, bağlantı vurgusu, okunaklı yazı, animasyon durdurma, büyük imleç. Tercihler ziyaretçinin tarayıcısında saklanır. **Erişilebilirlik** beyan sayfası otomatik oluşur (alt bilgide bağlantısı var).
- **Online takvim şeridi**: antrenör ve salon sayfalarında önümüzdeki 14 günün doluluğu (müsait / az yer / dolu); güne tıklayınca formda o günün saatleri açılır.
- **Antrenör > Sertifika / belge görselleri**: taranmış belgeleri yükleyin, profilde büyütülebilir görünür. "Hakkında" başlığı "Özgeçmiş" oldu; profilde verdiği paketler listelenir.
- **PT Paketleri** yeni alanlar: Seviye, Eğitim günleri (takvim), Kimler için, Program akışı, Bu paketi veren antrenörler. Paket kartlarında **Detaylar** düğmesi; paket sayfası kapsam, günler, program ve antrenör seçimini gösterir.
- **Antrenörler** sayfasında isim / uzmanlık araması.
- **Özelleştir > ArenaFit Tesis Olanakları**: soyunma odası, duş, otopark, kafe için fotoğraf, açıklama, ayrıntı maddeleri.
- **Üyelik** sayfası: süreç adımları, üye avantajları, Hesabım bağlantısı, sık sorulan sorular.

## İlk kurulum
1. **Görünüm > Temalar > Yeni Ekle > Tema Yükle** ile `arenafit.zip` dosyasını yükleyip etkinleştirin.
2. Ana Sayfa, Rezervasyon, Üyelik, Tesis Olanakları, Hesabım, İletişim sayfaları ve Ana Menü otomatik oluşturulur.
3. **Ayarlar > Kalıcı Bağlantılar**'a girip **Değişiklikleri Kaydet**'e tıklayın (bir kez).
4. **Görünüm > Özelleştir > ArenaFit Ayarları**: telefon, WhatsApp, adres, çalışma saatleri, bildirim e-postası, e-posta sunucusu (SMTP), harita, sosyal medya, ana sayfa görseli, üyelik planları.
5. Kendi logonuz varsa **Özelleştir > Site Kimliği**'nden yükleyin; yoksa tema logosu kullanılır. `assets/images/` içinde logonun SVG/PNG dosyaları ve 512 px site simgesi vardır.

## Bildirim e-postaları (rezervasyon sahibine düşsün)
Yeni rezervasyon, üyelik başvurusu, üye kaydı, mesaj/şikayet ve üyenin yaptığı iptal, **Rezervasyon bildirim e-postası** adresine gönderilir (boşsa site yöneticisi e-postası).
Rezervasyon e-postasında **Yanıtla** düğmesi müşteriye yanıt verir.

> **Önemli:** XAMPP/localhost gibi kendi bilgisayarınızda e-posta gönderilemez; hosting üzerinde de spam'e düşebilir.
> En güvenilir çözüm: **Özelleştir > ArenaFit Ayarları > E-posta sunucusu (SMTP)** alanlarını doldurmak (Gmail, Outlook veya hosting e-postası; eklenti gerekmez).
> Gmail için: sunucu `smtp.gmail.com`, port `587`, şifreleme `tls`, şifre olarak Google'dan alınan **Uygulama şifresi**.
> Ardından **Rezervasyonlar > E-posta Testi** sayfasından deneme e-postası gönderin; hata varsa nedeni orada yazar.
> E-posta gitmese de her rezervasyon panelde kayıtlıdır; hiçbir talep kaybolmaz.

Panel menüsünde **Rezervasyonlar** ve **Mesajlar / Şikayetler** yanında bekleyen sayısı görünür.

## İçerik girişi (yönetim paneli menüleri)
| Menü | Ne için |
|---|---|
| **Antrenörler** | Fotoğraf, unvan, özgeçmiş, eğitim, sertifikalar, resmi bilgiler, çalışma günü ve saatleri, uzmanlık alanları |
| **PT Paketleri** | Sitede satılan paket vitrini: bireysel/grup, seans sayısı, ücret, süre, günler, kapsam |
| **Salonlar** | Basketbol / voleybol, saatlik ücret, ölçü, zemin, özellikler, galeri, hakem hizmeti ve ücreti |
| **Rezervasyonlar** | Gelen talepler. Satırdaki **Onayla / İptal et** ile durum değişir, müşteriye e-posta gider |
| **Üye Paketleri** | Bir üyeye paket tanımlama (aşağıya bakın) |
| **Mesajlar / Şikayetler** | İletişim formundan gelenler; durum (Yeni / İnceleniyor / Çözüldü) ve yönetici notu |
| **Üyelik Başvuruları** | "Üyelik" sayfasındaki forma gelen başvurular |
| **Kullanıcılar** | Siteye kayıt olan üyeler (rol: **Üye**) |

## Üye girişi ve "Hesabım"
- Ziyaretçi **Hesabım** sayfasından e-posta + şifre ile **üye olur** veya giriş yapar. "Şifremi unuttum" WordPress'in standart akışını kullanır.
- Üye **yönetim paneline giremez**, üst çubuk görmez; giriş sonrası Hesabım sayfasına gider.
- Hesabım'da üye şunları görür: **paketi ve kalan seansı**, bitiş tarihi, yaklaşan ve geçmiş rezervasyonları, profil/şifre değiştirme.
- Üye, başlangıca **12 saatten fazla** varsa rezervasyonunu kendisi iptal edebilir (süre `ARENAFIT_CANCEL_HOURS`, `inc/account.php`). Yaklaşan iptallerde "bizi arayın" mesajı çıkar. İptal sahibe e-postayla bildirilir.
- Giriş yapmış üyenin rezervasyon ve mesaj formları ad, telefon, e-postayla önceden dolu gelir; rezervasyonu Hesabım'da görünür.

- Giriş denemesi sınırı: yalnızca **hatalı** denemeler sayılır (IP + e-posta başına 8 hata → 15 dakika bekleme); başarılı girişte sayaç sıfırlanır. Süre/sayı `ARENAFIT_LOGIN_MAX` ve `ARENAFIT_LOGIN_WINDOW` ile değişir (`inc/account.php`).
- "Şifremi unuttum" e-posta ile çalışır; e-posta kurulmadıysa (yukarıdaki SMTP bölümü) bir üyenin şifresini **Kullanıcılar > ilgili kullanıcı > Yeni şifre** ile siz belirleyebilirsiniz.

### Üyeye paket tanımlama (siz yaparsınız)
1. Üye siteden kayıt olur (veya **Kullanıcılar > Yeni Ekle** ile rolü "Üye" olarak siz eklersiniz).
2. **Üye Paketleri > Pakete Üye Tanımla**: üyeyi, paketi, seans hakkını, başlangıç/bitiş tarihini seçin; isterseniz üyenin göreceği bir not yazın (örn. "Salı ve Perşembe 19:00").
3. Üye seansa geldikçe **Kullanılan seans** sayısını artırın. Üyenin sayfasında "X seans kaldı" ve ilerleme çubuğu güncellenir.
4. Dondurmak için durumu "İptal / dondurulmuş" yapın.

## İletişim / Şikayet
**İletişim** sayfasındaki form: konu türü (Genel / Şikayet / Öneri / Bilgi talebi), ad, telefon, e-posta, mesaj. Gönderene kayıt numarası (AF-123) verilir; size e-posta düşer ve panelde kayıtlı kalır. Menüden/Hesabım'dan `?tur=complaint` bağlantısıyla "Şikayet" önceden seçili açılır.

## Rezervasyon mantığı
- Her antrenör ve her salon ayrı takvimdir (2 basketbol + 1 voleybol salonu = 3 ayrı salon kaydı).
- Boş/dolu saatler anlık hesaplanır; onay bekleyen ve onaylı rezervasyonlar saati kapatır, iptal edilenler açar. Aynı saate çifte rezervasyon engellenir.
- Antrenör çalışma günü/saati dışında saat gösterilmez. Salonlarda 1-2-3 saatlik süre seçilir.
- Rezervasyon en fazla 60 gün ilerisine alınır (`ARENAFIT_HORIZON`, `inc/booking.php`).

## Kısa kodlar (Elementor vb. ile kullanım)
- `[arenafit_booking]` – genel rezervasyon formu
- `[arenafit_booking kind="hall" item="12" lock="1"]` – belirli salon/antrenör için sabit form
- `[arenafit_membership]` – üyelik başvuru formu
- `[arenafit_account]` – üye girişi / Hesabım
- `[arenafit_contact]` – iletişim / şikayet formu

## Notlar
- Telefon, tablet ve bilgisayar için uyumludur; menü 1180 px altında açılır menüye döner.
- Yazı tipi olarak sistem yazı tipleri kullanılır (harici Google Fonts çağrısı yok; KVKK açısından da rahat).
- Ön belleğe alma (cache) eklentisi kullanıyorsanız `admin-ajax.php`, `admin-post.php` ve **Hesabım** sayfasını önbellek dışında bırakın.
- KVKK onayı için Özelleştir'den aydınlatma metni adresini girin veya WordPress Gizlilik Politikası sayfasını belirleyin.
- SMTP şifresi veritabanında saklanır; mümkünse yalnızca bu iş için açılmış bir e-posta hesabı/uygulama şifresi kullanın.
