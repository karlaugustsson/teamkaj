<?php namespace App\Http\Controllers;
use App\Category;
use App\Product;
use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CategoriesController extends Controller {

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$products = Product::paginate(12);
    	$categories = Category::all();

		return view('pages.products', compact('products', 'categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$categories = Category::all();

		return view('pages.createcategory', compact('categories', $categories));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(CategoryRequest $request)
	{
		$slug = $this->slugify($request->name);

		$categories = Category::create($request->all());
		$categories->update(['slug' => $slug]);					
		

		return redirect('categories/create');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($slug)
	{
		
		$category = Category::where('slug', '=', $slug)->firstOrFail();
		$products = $category->products;

		return view('pages.categories', compact('category', 'products'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($slug)
	{
		$category = Category::where('slug', '=', $slug)->firstOrFail();
	
		return view('pages.editcategory', compact('category', $category));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, CategoryRequest $request)
	{
		$category = Category::where('id', '=', $id)->firstOrFail();
		$slug = $this->slugify($request->name);

	    $category->update($request->all());
	    $category->update(['slug' => $slug]);

	    return redirect('categories/create');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$category = Category::where('id', '=', $id)->firstOrFail();
	    $category->delete();

	    return redirect('categories/create');
	}


	
	/* Generates a slug from the name */
	public function slugify($name)
	{
		$slug = str_replace(" ", "-", $name);
		return $slug;
	}


}
