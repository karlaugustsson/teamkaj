<?php namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use App\Http\Controllers\Controller;

use App\Category;
use App\Product;


use Illuminate\Http\Request;

class CategoriesController extends Controller {

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);

        $this->middleware('admin', ['only' => ['create', 'store', 'edit', 'update', 'destroy', 'deletecategory']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$products = Product::
					where('published', '!=', 0)
					->paginate(12);
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

		return view('pages.createCategory', compact('categories', $categories));
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
	public function show($slug, Request $request)
	{
		//dd($input);
		//dd($request->input('sort'));
        $category = Category::where('slug', '=', $slug)->firstOrFail();

		$products = $this->sort($category->products(), $request->input('sort'))->get();

		//dd($products);

		$sort = "name";

		if ($request->input('sort'))
        	$sort = $request->input('sort');

		return view('pages.categories', compact('category', 'products', 'sort'));
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
	
		return view('pages.editCategory', compact('category', $category));
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

    public function deletecategory($id)
    {
        $category = Category::where('id', '=', $id)->firstOrFail();

        return view('pages.deletecategory', compact('category'));
    }


	
	/* Generates a slug from the name */
	public function slugify($name)
	{
		$slug = strtolower(str_replace(" ", "-", $name));
		return $slug;
	}


	public function sort($products, $input)

	{
		switch ($input) {

				case 'created_atDesc':

					$products->orderBy('created_at', 'DESC');
					break;

				case 'created_atAsc':
					$products->orderBy('created_at');
					break;

				case 'priceAsc':
					$products->orderBy('price');
					break;

				case 'priceDesc':
					$products->orderBy('price', 'DESC');
					break;

				case 'nameAsc':
					$products->orderBy('name');
					break;

				case 'nameDesc':
					$products->orderBy('name', 'DESC');
					break;
				
				default:
					$products->orderBy('created_at', 'DESC');

					break;
				}
		return $products;
	}

}
