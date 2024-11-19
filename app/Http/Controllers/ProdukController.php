<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *
     */
    public function ViewProduk()
    {
        $isAdmin = Auth::user()->role == 'admin';

        $produk = $isAdmin ? Produk::all() : Produk::where('user_id', Auth::user()->id)->get(); //mengambil semua data di tabel produk

        return view('produk', ['produk' => $produk]); //menampilkan view dari produk.blade.php dengan membawa variabel Sproduk
    }

    public function CreateProduk(Request $request)
    {
        $imageName = null;
        if($request->hasFile('image')){
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/images', $imageName);
        }

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'jumlah_produk' => $request->jumlah_produk,
            'image' => $imageName,
            'user_id' => Auth::user()->id
        ]);

        return redirect(Auth::user()->role.'/produk');
    }
    public function ViewAddProduk()
    {
        return view('addproduk');
    }

    public function DeleteProduk($kode_produk)
    {
        Produk::where('kode_produk', $kode_produk)->delete();

        return redirect(Auth::user()->role.'/produk');
    }

    public function ViewEditProduk ($kode_produk)
    {
        $ubah_produk = Produk :: where('kode_produk', $kode_produk)->first();

        return view('editproduk', compact('ubah_produk'));
    }

    public function UpdateProduk(Request $request,$kode_produk)
    {
        $imageName = null;
        if($request->hasFile('image')){
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/images', $imageName);
        }

        Produk ::where('kode_produk', $kode_produk)->update([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'jumlah_produk' => $request->jumlah_produk,
            'image' => $imageName,
            'user_id' => Auth::user()->id
        ]);

        return redirect(Auth::user()->role.'/produk');
    }

    public function ViewLaporan()
    {
        //Mengambil semua data produk
        $isAdmin = Auth::user()->role == 'admin';
        $products = $isAdmin ? Produk::all() : Produk::where('user_id', Auth::user()->id)->get();
        return view('laporan', ['products' => $products]);
    }

    public function print()
    {
        //Mengambil semua data produk
        $products = Produk::all();

        //Load view untuk pdf dengan data produk
        $pdf = Pdf::loadView('report', compact('products'));

        //Menampilkan PDF langsung di browser
        return $pdf->stream('laporan-produk.pdf');
    }

}
