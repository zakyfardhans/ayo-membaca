<?php

namespace Database\Seeders;

use App\Models\Books;
use App\Models\Categories;
use App\Services\BookCoverService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        $makeBook = static fn(string $category, string $isbn, string $title, string $author, string $publisher, int $year, string $cover, string $synopsis): array => [
            'category_slug' => $category,
            'isbn' => $isbn,
            'title' => $title,
            'author' => $author,
            'publisher' => $publisher,
            'publication_year' => $year,
            'stock' => 5,
            'cover_image' => 'covers/' . $cover,
            'synopsis' => $synopsis,
        ];

        $books = [
            // Fiksi
            $makeBook('fiksi', '9789799731230', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 1980, 'bumi-manusia.jpg', 'Novel yang mengisahkan kehidupan Minke, seorang pribumi terpelajar pada masa kolonial Hindia Belanda, serta perjuangannya menghadapi ketidakadilan sosial dan kolonialisme.'),
            $makeBook('fiksi', '9789793062798', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'laskar-pelangi.jpg', 'Kisah perjuangan sepuluh anak Belitung dalam memperoleh pendidikan dan mengejar cita-cita di tengah keterbatasan ekonomi.'),
            $makeBook('fiksi', '9786024246945', 'Laut Bercerita', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', 2017, 'laut-bercerita.jpg', 'Novel tentang persahabatan, keluarga, aktivisme, dan kehilangan yang berlatar peristiwa politik Indonesia pada akhir Orde Baru.'),
            $makeBook('fiksi', '9789799103488', 'Cantik Itu Luka', 'Eka Kurniawan', 'Gramedia Pustaka Utama', 2002, 'cantik-itu-luka.jpg', 'Novel yang memadukan realisme, sejarah, mitos, dan humor melalui kisah keluarga Dewi Ayu dan keturunannya.'),
            $makeBook('fiksi', '9789792227532', 'Ronggeng Dukuh Paruk', 'Ahmad Tohari', 'Gramedia Pustaka Utama', 1982, 'ronggeng-dukuh-paruk.jpg', 'Kisah Srintil, seorang ronggeng dari Dukuh Paruk, yang kehidupannya berubah akibat tradisi, cinta, dan pergolakan politik.'),
            $makeBook('fiksi', '9786022916628', 'Perahu Kertas', 'Dee Lestari', 'Bentang Pustaka', 2009, 'perahu-kertas.jpg', 'Kisah Kugy dan Keenan yang dipertemukan oleh mimpi, kreativitas, cinta, dan perjalanan hidup.'),
            $makeBook('fiksi', '9786027870383', 'Dilan: Dia Adalah Dilanku Tahun 1990', 'Pidi Baiq', 'Pastel Books', 2014, 'dilan.jpg', 'Kisah cinta remaja antara Milea dan Dilan yang berlatar kehidupan sekolah di Bandung pada tahun 1990.'),
            $makeBook('fiksi', '9786022918325', 'Aroma Karsa', 'Dee Lestari', 'Bentang Pustaka', 2018, 'aroma-karsa.jpg', 'Novel tentang pencarian aroma legendaris yang mempertemukan kemampuan penciuman istimewa, sejarah, dan ambisi.'),
            $makeBook('fiksi', '9780156012195', 'The Little Prince', 'Antoine de Saint-Exupéry', 'Harcourt', 1943, 'the-little-prince.jpg', 'Kisah seorang pilot yang bertemu seorang pangeran kecil dari asteroid lain dan belajar tentang persahabatan, cinta, serta kehidupan.'),
            $makeBook('fiksi', '9780451524935', '1984', 'George Orwell', 'Signet Classics', 1949, '1984.jpg', 'Novel distopia tentang Winston Smith yang hidup dalam masyarakat yang diawasi dan dikendalikan oleh rezim totaliter.'),

            // Pemrograman
            $makeBook('pemrograman', '9780132350884', 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 'clean-code.jpg', 'Panduan mengenai prinsip dan praktik untuk menulis kode yang mudah dibaca, dipahami, dan dipelihara.'),
            $makeBook('pemrograman', '9780135957059', 'The Pragmatic Programmer', 'Andrew Hunt & David Thomas', 'Addison-Wesley', 2019, 'the-pragmatic-programmer.jpg', 'Buku mengenai prinsip dan praktik pengembangan perangkat lunak yang membantu programmer meningkatkan kualitas kerja.'),
            $makeBook('pemrograman', '9780262510875', 'Structure and Interpretation of Computer Programs', 'Harold Abelson & Gerald Jay Sussman', 'MIT Press', 1996, 'sicp.jpg', 'Buku klasik ilmu komputer yang membahas konsep dasar pemrograman, abstraksi, dan struktur program.'),
            $makeBook('pemrograman', '9780201633610', 'Design Patterns', 'Erich Gamma dkk.', 'Addison-Wesley', 1994, 'design-patterns.jpg', 'Referensi pola desain perangkat lunak yang umum digunakan untuk menyelesaikan masalah desain berulang.'),
            $makeBook('pemrograman', '9780262046305', 'Introduction to Algorithms', 'Thomas H. Cormen dkk.', 'MIT Press', 2009, 'introduction-to-algorithms.jpg', 'Buku komprehensif mengenai algoritma, struktur data, analisis algoritma, dan teknik pemecahan masalah komputasional.'),
            $makeBook('pemrograman', '9780134685991', 'Effective Java', 'Joshua Bloch', 'Addison-Wesley', 2017, 'effective-java.jpg', 'Panduan praktik terbaik untuk menulis kode Java yang efektif, aman, dan mudah dipelihara.'),
            $makeBook('pemrograman', '9781718502703', 'Python Crash Course', 'Eric Matthes', 'No Starch Press', 2019, 'python-crash-course.jpg', 'Buku pembelajaran Python yang mengajarkan konsep dasar pemrograman dan pembuatan beberapa proyek praktis.'),
            $makeBook('pemrograman', '9781593279509', 'Eloquent JavaScript', 'Marijn Haverbeke', 'No Starch Press', 2018, 'eloquent-javascript.jpg', 'Panduan JavaScript yang membahas dasar bahasa, pemrograman fungsional, struktur data, browser, dan Node.js.'),
            $makeBook('pemrograman', '9781091210090', "You Don't Know JS Yet", 'Kyle Simpson', 'O’Reilly Media', 2020, 'you-dont-know-js-yet.jpg', 'Seri buku yang membahas JavaScript secara mendalam, termasuk konsep inti bahasa dan mekanisme eksekusinya.'),
            $makeBook('pemrograman', '9780735619678', 'Code Complete', 'Steve McConnell', 'Microsoft Press', 2004, 'code-complete.jpg', 'Panduan praktis mengenai konstruksi perangkat lunak, kualitas kode, debugging, dan praktik pengembangan yang efektif.'),

            // Sejarah
            $makeBook('sejarah', '9786024125182', 'Sejarah Dunia yang Disembunyikan', 'Jonathan Black', 'Pustaka Alvabet', 2007, 'sejarah-dunia-yang-disembunyikan.jpg', 'Buku yang membahas sejarah melalui perspektif alternatif dan berbagai tradisi pemikiran sepanjang peradaban manusia.'),
            $makeBook('sejarah', '9789794037559', 'Nusantara: Sejarah Indonesia', 'Bernard H.M. Vlekke', 'Kepustakaan Populer Gramedia', 2016, 'nusantara.jpg', 'Kajian mengenai perjalanan sejarah kepulauan Indonesia dari masa awal hingga perkembangan negara modern.'),
            $makeBook('sejarah', '9786024246082', 'Indonesia dalam Arus Sejarah', 'Berbagai Penulis', 'Ichtiar Baru van Hoeve', 2012, 'indonesia-dalam-arus-sejarah.jpg', 'Kumpulan kajian sejarah Indonesia yang membahas berbagai periode, peristiwa, dan aspek kehidupan masyarakat.'),
            $makeBook('sejarah', '9786024411224', 'Revolusi Pancasila', 'Yudi Latif', 'Mizan', 2015, 'revolusi-pancasila.jpg', 'Pembahasan mengenai sejarah pemikiran dan perkembangan Pancasila dalam perjalanan Indonesia.'),
            $makeBook('sejarah', '9786024247461', 'Madiun 1948: PKI Bergerak', 'Harry A. Poeze', 'Yayasan Pustaka Obor Indonesia', 2011, 'madiun-1948.jpg', 'Kajian sejarah mengenai peristiwa Madiun 1948 dan dinamika politik yang melingkupinya.'),
            $makeBook('sejarah', '9786024333127', 'Tan Malaka, Gerakan Kiri, dan Revolusi Indonesia', 'Harry A. Poeze', 'Yayasan Pustaka Obor Indonesia', 2008, 'tan-malaka.jpg', 'Kajian biografis dan historis mengenai Tan Malaka serta keterlibatannya dalam gerakan politik dan revolusi Indonesia.'),
            $makeBook('sejarah', '9780307389001', 'The History of the Decline and Fall of the Roman Empire', 'Edward Gibbon', 'Modern Library', 1776, 'decline-fall-roman-empire.jpg', 'Karya sejarah monumental yang membahas perkembangan dan kemunduran Kekaisaran Romawi.'),
            $makeBook('sejarah', '9780393317558', 'Guns, Germs, and Steel', 'Jared Diamond', 'W. W. Norton', 1997, 'guns-germs-and-steel.jpg', 'Penjelasan mengenai faktor geografis, ekologis, dan teknologi yang memengaruhi perkembangan masyarakat manusia.'),
            $makeBook('sejarah', '9781631492228', 'SPQR', 'Mary Beard', 'Liveright', 2015, 'spqr.jpg', 'Sejarah populer mengenai Republik dan Kekaisaran Romawi serta masyarakat yang membentuk peradaban tersebut.'),
            $makeBook('sejarah', '9780060838652', "A People's History of the United States", 'Howard Zinn', 'Harper Perennial', 1980, 'a-peoples-history.jpg', 'Sejarah Amerika Serikat yang menyoroti pengalaman dan perspektif berbagai kelompok masyarakat.'),

            // Sains
            $makeBook('sains', '9780553380163', 'A Brief History of Time', 'Stephen Hawking', 'Bantam', 1988, 'a-brief-history-of-time.jpg', 'Pengantar populer mengenai alam semesta, ruang-waktu, lubang hitam, dan pertanyaan fundamental dalam kosmologi.'),
            $makeBook('sains', '9780345539434', 'Cosmos', 'Carl Sagan', 'Random House', 1980, 'cosmos.jpg', 'Eksplorasi mengenai alam semesta, sejarah sains, evolusi, dan posisi manusia dalam kosmos.'),
            $makeBook('sains', '9780198788607', 'The Selfish Gene', 'Richard Dawkins', 'Oxford University Press', 1976, 'the-selfish-gene.jpg', 'Pembahasan evolusi dari sudut pandang gen dan bagaimana seleksi alam dapat dipahami melalui perspektif tersebut.'),
            $makeBook('sains', '9780451529060', 'The Origin of Species', 'Charles Darwin', 'Signet Classics', 1859, 'origin-of-species.jpg', 'Karya Darwin yang memperkenalkan teori evolusi melalui seleksi alam.'),
            $makeBook('sains', '9780062316097', 'Sapiens', 'Yuval Noah Harari', 'Harper', 2015, 'sapiens.jpg', 'Gambaran luas mengenai perjalanan Homo sapiens dari masa prasejarah hingga masyarakat modern.'),
            $makeBook('sains', '9780380715439', 'A Short History of Nearly Everything', 'Bill Bryson', 'Broadway Books', 2003, 'a-short-history.jpg', 'Penjelasan populer mengenai sejarah alam semesta, bumi, kehidupan, dan perkembangan ilmu pengetahuan.'),
            $makeBook('sains', '9781476733500', 'The Gene', 'Siddhartha Mukherjee', 'Scribner', 2016, 'the-gene.jpg', 'Sejarah dan perkembangan ilmu genetika serta dampaknya terhadap pemahaman manusia tentang kehidupan dan keturunan.'),
            $makeBook('sains', '9780393338102', 'The Elegant Universe', 'Brian Greene', 'W. W. Norton', 1999, 'the-elegant-universe.jpg', 'Pengantar populer mengenai teori relativitas, mekanika kuantum, dan teori string.'),
            $makeBook('sains', '9780393339911', 'The Fabric of the Cosmos', 'Brian Greene', 'Vintage', 2003, 'the-fabric-of-the-cosmos.jpg', 'Pembahasan populer mengenai ruang, waktu, realitas, dan konsep fundamental fisika modern.'),
            $makeBook('sains', '9780345425686', 'The Demon-Haunted World', 'Carl Sagan', 'Random House', 1995, 'the-demon-haunted-world.jpg', 'Pembelaan terhadap pemikiran ilmiah dan skeptisisme dalam menghadapi klaim pseudoscience.'),

            // Filsafat
            $makeBook('filsafat', '9789799731231', 'Madilog', 'Tan Malaka', 'Narasi', 1943, 'madilog.jpg', 'Karya pemikiran Tan Malaka mengenai materialisme, dialektika, dan logika sebagai cara berpikir rasional.'),
            $makeBook('filsafat', '9786024411095', 'Dunia Sophie', 'Jostein Gaarder', 'Mizan', 1991, 'dunia-sophie.jpg', 'Novel filosofis yang memperkenalkan sejarah pemikiran filsafat melalui perjalanan seorang gadis bernama Sophie.'),
            $makeBook('filsafat', '9780872201361', 'The Republic', 'Plato', 'Hackett Publishing', -380, 'the-republic.jpg', 'Dialog filsafat Plato yang membahas keadilan, negara ideal, pendidikan, dan hakikat pengetahuan.'),
            $makeBook('filsafat', '9780140449334', 'Meditations', 'Marcus Aurelius', 'Penguin Classics', 180, 'meditations.jpg', 'Catatan reflektif Marcus Aurelius mengenai kehidupan, kebajikan, pengendalian diri, dan cara menghadapi kesulitan.'),
            $makeBook('filsafat', '9780140449235', 'Beyond Good and Evil', 'Friedrich Nietzsche', 'Penguin Classics', 1886, 'beyond-good-and-evil.jpg', 'Kritik Nietzsche terhadap moralitas tradisional dan filsafat dogmatis.'),
            $makeBook('filsafat', '9780140447882', 'Thus Spoke Zarathustra', 'Friedrich Nietzsche', 'Penguin Classics', 1883, 'thus-spoke-zarathustra.jpg', 'Karya filosofis Nietzsche yang disampaikan melalui tokoh Zarathustra dan membahas manusia, nilai, serta penciptaan makna.'),
            $makeBook('filsafat', '9780679733737', 'The Myth of Sisyphus', 'Albert Camus', 'Vintage', 1942, 'the-myth-of-sisyphus.jpg', 'Esai Camus mengenai absurditas kehidupan dan persoalan bagaimana manusia menemukan makna di tengah absurditas.'),
            $makeBook('filsafat', '9780872204201', 'Discourse on Method', 'René Descartes', 'Hackett Publishing', 1637, 'discourse-on-method.jpg', 'Karya Descartes mengenai metode berpikir rasional dan pencarian dasar pengetahuan yang pasti.'),
            $makeBook('filsafat', '9780521657295', 'Critique of Pure Reason', 'Immanuel Kant', 'Cambridge University Press', 1781, 'critique-of-pure-reason.jpg', 'Karya utama Kant yang menyelidiki batas, struktur, dan kemungkinan pengetahuan manusia.'),
            $makeBook('filsafat', '9780140447576', 'The Communist Manifesto', 'Karl Marx & Friedrich Engels', 'Penguin Classics', 1848, 'the-communist-manifesto.jpg', 'Pamflet politik dan sosial yang membahas sejarah perjuangan kelas serta kritik terhadap kapitalisme.'),

            // Teknologi
            $makeBook('teknologi', '9781451688096', 'The Innovators', 'Walter Isaacson', 'Simon & Schuster', 2014, 'the-innovators.jpg', 'Kisah para inovator dan perkembangan teknologi komputer serta internet yang membentuk era digital.'),
            $makeBook('teknologi', '9781451648539', 'Steve Jobs', 'Walter Isaacson', 'Simon & Schuster', 2011, 'steve-jobs.jpg', 'Biografi Steve Jobs berdasarkan wawancara dan berbagai sumber yang membahas kehidupan serta kariernya di dunia teknologi.'),
            $makeBook('teknologi', '9780393239355', 'The Second Machine Age', 'Erik Brynjolfsson & Andrew McAfee', 'W. W. Norton', 2014, 'the-second-machine-age.jpg', 'Pembahasan mengenai dampak teknologi digital, komputasi, dan otomatisasi terhadap ekonomi dan masyarakat.'),
            $makeBook('teknologi', '9781101970310', 'Life 3.0', 'Max Tegmark', 'Vintage', 2017, 'life-3.jpg', 'Eksplorasi mengenai kecerdasan buatan dan kemungkinan perubahan kehidupan manusia ketika AI menjadi semakin maju.'),
            $makeBook('teknologi', '9780198739838', 'Superintelligence', 'Nick Bostrom', 'Oxford University Press', 2014, 'superintelligence.jpg', 'Kajian mengenai kemungkinan perkembangan kecerdasan mesin dan berbagai tantangan yang dapat muncul dari sistem AI yang sangat maju.'),
            $makeBook('teknologi', '9780316273009', 'The Age of AI', 'Henry Kissinger, Eric Schmidt & Daniel Huttenlocher', 'Little, Brown and Company', 2021, 'the-age-of-ai.jpg', 'Pembahasan mengenai perkembangan kecerdasan buatan dan implikasinya terhadap pengetahuan, masyarakat, dan tatanan dunia.'),
            $makeBook('teknologi', '9780670034575', 'The Singularity Is Near', 'Ray Kurzweil', 'Viking', 2005, 'the-singularity-is-near.jpg', 'Pembahasan mengenai perkembangan teknologi dan prediksi tentang masa depan ketika kecerdasan buatan dan manusia semakin terintegrasi.'),
            $makeBook('teknologi', '9780393339111', 'The Shallows', 'Nicholas Carr', 'W. W. Norton', 2010, 'the-shallows.jpg', 'Kajian mengenai bagaimana internet dan teknologi digital dapat memengaruhi cara manusia membaca, berpikir, dan memproses informasi.'),
            $makeBook('teknologi', '9780393336433', 'The Code Book', 'Simon Singh', 'Anchor', 1999, 'the-code-book.jpg', 'Sejarah kriptografi yang membahas perkembangan kode dan teknik penyandian dari masa lalu hingga era komputer.'),
            $makeBook('teknologi', '9781449388393', 'Hackers: Heroes of the Computer Revolution', 'Steven Levy', 'O’Reilly Media', 1984, 'hackers.jpg', 'Sejarah perkembangan budaya hacker dan tokoh-tokoh yang berperan dalam revolusi komputer.'),
        ];

        $categories = Categories::pluck('id', 'slug');
        $missingCategories = collect($books)->pluck('category_slug')->unique()->diff($categories->keys());

        if ($missingCategories->isNotEmpty()) {
            throw new RuntimeException('Missing book categories: ' . $missingCategories->implode(', '));
        }

        $isbnList = collect($books)->pluck('isbn');
        if ($isbnList->count() !== $isbnList->unique()->count()) {
            throw new RuntimeException('The curated book list contains duplicate ISBN values.');
        }

        $existingCovers = Books::withTrashed()->pluck('cover_image')->filter();
        $newCovers = collect($books)->pluck('cover_image')->filter();
        $coversToDelete = $existingCovers->diff($newCovers)->unique()->values();

        DB::transaction(function () use ($books, $categories, $isbnList): void {
            foreach ($books as $bookData) {
                $bookData['category_id'] = $categories[$bookData['category_slug']];
                unset($bookData['category_slug']);

                $book = Books::withTrashed()->firstOrNew(['isbn' => $bookData['isbn']]);
                $book->fill($bookData);
                $book->deleted_at = null;
                $book->save();
            }

            Books::withTrashed()->whereNotIn('isbn', $isbnList)->forceDelete();
        });

        $coverService = app(BookCoverService::class);
        $coversToDelete->each(fn(string $path) => $coverService->delete($path));

    }
}
