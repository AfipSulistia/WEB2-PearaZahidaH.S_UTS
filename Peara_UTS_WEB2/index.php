<?php
require_once "liblary.php";
require_once "book.php";
require_once "references.php";
session_start();

if (!isset($_SESSION['liblary'])) {
    $_SESSION['liblary'] = new Liblary();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addBook'])) {
        $isbn = $_POST['isbn'];
        $judul = $_POST['judul'];
        $penulis = $_POST['penulis'];
        $penerbit = $_POST['penerbit'];
        $tahun = $_POST['tahun'];

        $newBook = new ReferenceBook($judul, $penulis, $tahun, $isbn, $penerbit);
        $_SESSION['liblary']->addBook($newBook);
    }
    if (isset($_POST['removeBook'])) {

        if (isset($_POST['bookId'])) {

            $isbn = $_POST['bookId'];
            if (isset($_SESSION['liblary'])) {
                $_SESSION['liblary']->removeBook($isbn);
            }
        }
    }

    if (isset($_POST['pinjamBook'])) {
        $isbn = $_POST['isbn'];
        $peminjam = $_POST['peminjam'];
        $tanggal_kembali = $_POST['tanggal'];

        if ($_SESSION['liblary']->checkBorrowerLimit($peminjam)) {
            $book = $_SESSION['liblary']->findBookByISBN($isbn);

            if ($book) {
                $book->borrowBook($peminjam, $tanggal_kembali);
                $_SESSION['liblary']->saveToSession();
            }
        }
    }

    if (isset($_POST['returnBook'])) {
        $isbn = $_POST['isbn'];

        $book = $_SESSION['liblary']->findBookByISBN($isbn);

        if ($book) {
            $book->returnBook();
            $_SESSION['liblary']->saveToSession();
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Dea Meilani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body class="bg-gray-100">
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand text-light" href="#">Peara</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active text-light" aria-current="page" href="index.php">Home</a>
        </li>
    
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Menu
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="tambahbuku.php">Tambah Buku</a></li>
            <li><a class="dropdown-item" href="kembalikanbuku.php">Kembalikan Buku</a></li>
            
          </ul>
        </li>
       
      </ul>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" name="keyword" aria-label="Search">
        <button class="btn btn-outline-success" type="submit" id="button-addon2">Search</button>
      </form>
    </div>
  </div>
</nav>  
<div class="modal" id="modalPinjam">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Pinjam Buku?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="modalISBN" name="isbn" required>
                        <div class="mb-3">
                            <label for="modalPeminjam" class="form-label">Nama Peminjam</label>
                            <input type="text" class="form-control" name="peminjam" id="modalPeminjam" required>
                        </div>
                        <div class="input-group date mb-3" id="datepicker">
                            <input type="date" class="form-control" name="tanggal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="pinjamBook" class="btn btn-primary">Ya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Hapus -->
    <div class="modal" id="modalHapus">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Hapus Buku?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus buku ini?</p>
                        <input type="hidden" name="bookId" id="bookId" value="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="removeBook" class="btn btn-primary">Ya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container mx-auto my-5" id="main-container">
        <div class="mx-auto px-5">
           
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="sort" class="form-label">Urutkan Berdasarkan</label>
                    <select class="form-select w-25" aria-label="Urutkan Buku" id="sort" name="sort">
                        <option selected value="penulis">Penulis</option>
                        <option value="tahun">Tahun Terbit</option>
                    </select>
                </div>
                <button type="submit" name="apply_sort" class="btn btn-primary">Terapkan Sorting</button>
            </form>
        </div>

        <div class="book-container text-center" id="book-container">
            <h3 class="my-5">List Buku Tersedia</h3>
            <div class="grid grid-cols-3 gap-4 justify-center" id="book-grid">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_sort'])) {
                    $sortCriteria = $_POST['sort'];

                    $sortedBooks = $_SESSION['liblary']->sortBooks($sortCriteria);

                    foreach ($sortedBooks as $book) {
                        if (!$book->isBorrowed()) {
                            echo "<div class='card'>";
                            echo "<h5 class='card-header'>" . $book->getTitle() . "</h5>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $book->getAuthor() . "</h5>";
                            echo "<p class='card-text'>" . $book->getYear() . "</p>";
                            echo "<p class='card-text'>" . $book->getPublisher() . "</p>";
                            echo "<div class='d-flex flex-col gap-2 justify-content-center'>";
                            echo "<a type='button' class='btn btn-primary btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'>Pinjam</a>";
                            echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'>Hapus</a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
                    $keyword = $_POST['keyword'];
                    $searchResults = $_SESSION['liblary']->searchBooks($keyword);
                    foreach ($searchResults as $book) {
                        if (!$book->isBorrowed()) {
                            echo "<div class='card'>";
                            echo "<h5 class='card-header'>" . $book->getTitle() . "</h5>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $book->getAuthor() . "</h5>";
                            echo "<p class='card-text'>" . $book->getYear() . "</p>";
                            echo "<p class='card-text'>" . $book->getPublisher() . "</p>";
                            echo "<div class='d-flex flex-col gap-2 justify-content-center'>";
                            echo "<a type='button' class='btn btn-primary btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'>Pinjam</a>";
                            echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'>Hapus</a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                } else {
                    foreach ($_SESSION['liblary']->getBooks() as $book) {
                        if (!$book->isBorrowed()) {
                            echo "<div class='card'>";
                            echo "<h5 class='card-header'>" . $book->getTitle() . "</h5>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $book->getAuthor() . "</h5>";
                            echo "<p class='card-text'>" . $book->getYear() . "</p>";
                            echo "<p class='card-text'>" . $book->getPublisher() . "</p>";
                            echo "<div class='d-flex flex-col gap-2 justify-content-center'>";
                            echo "<a type='button' class='btn btn-primary btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'>Pinjam</a>";
                            echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'>Hapus</a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                }
                ?>
            </div>
        </div>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).on("click", ".btn-hapus", function() {
            var isbn = $(this).data('isbn');
            $(".modal-body #bookId").val(isbn);
        });
        $(document).on("click", ".btn-pinjam", function() {
            var isbn = $(this).data('isbn');
            $(".modal-body #modalISBN").val(isbn);
        });
    </script>
</body>

</html>
