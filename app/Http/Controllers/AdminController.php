<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    // Returns the admin dashboard view 
    public function index()
    {
        return view('admin.index');
    }

    // Returns the brands view in the admin dashboard
    // Retrieves all brands from the database, ordered by ID in descending order
    public function brands()
    {
        $brands = Brand::orderBy('id', 'desc')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    // Returns the view to add a new brand
    public function addBrand()
    {
        return view('admin.brand-add');
    }

    // Handles the storage of a new brand
    // Validates the request, processes the image, and saves the brand to the database
    // Redirects back to the brands page with a success message
    public function storeBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand added successfully!');
    }

    // Handles the update of a brand and returns the edit view
    public function editBrand($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    // Validates the request, processes the image if provided, updates the brand in the database
    // Redirects back to the brands page with a success message
    // If the image is updated, it deletes the old image file from the server
    // Generates a new thumbnail for the updated image
    // Uses the GenerateBrandThumbnailsImage method to create a thumbnail for the brand image
    // The thumbnail is resized to 124x124 pixels and saved in the uploads/brands directory
    // The method also handles the case where the image is not provided, ensuring that the brand
    // name and slug are still updated without changing the image
    // The slug is generated from the brand name using the Str::slug method
    // The method returns a redirect response to the brands page with a success message
    public function updateBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $request->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;

            if(File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand updated successfully!');
    }

    // Generates a thumbnail for the brand image
    // The thumbnail is resized to 124x124 pixels and saved in the uploads/brands directory
    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());

        $img->cover(124, 124, "top");
        $img->resize(124, 124, function($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath. '/' . $imageName);
    }

    // Deletes a brand by its ID
    // Checks if the brand exists, deletes the image file from the server if it exists,
    // and then deletes the brand from the database
    // Redirects back to the brands page with a success message
    // If the brand does not exist, it redirects back with an error message
    public function deleteBrand($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            if (File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
            $brand->delete();
            return redirect()->route('admin.brands')->with('status', 'Brand Deleted successfully!');
        }
        return redirect()->route('admin.brands')->with('error', 'Brand not found!');
    }

    // Returns the categories view in the admin dashboard
    // Retrieves all categories from the database, ordered by ID in descending order
    public function categories()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    // Returns the view to add a new category
    public function addCategory()
    {
        return view('admin.category-add');
    }

    // Handles the storage of a new category
    // Validates the request, processes the image, and saves the category to the database
    // Redirects back to the categories page with a success message
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateCategoryThumbnailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category added successfully!');
    }

    // Handles the update of a category and returns the edit view
    public function editCategory($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    // Validates the request, processes the image if provided, updates the category in the database
    // Redirects back to the categories page with a success message
    // If the image is updated, it deletes the old image file from the server
    // Generates a new thumbnail for the updated image
    // Uses the GenerateCategoryThumbnailsImage method to create a thumbnail for the category image
    // The thumbnail is resized to 124x124 pixels and saved in the uploads/categories directory
    // The method also handles the case where the image is not provided, ensuring that the category
    // name and slug are still updated without changing the image
    // The slug is generated from the category name using the Str::slug method
    // The method returns a redirect response to the categories page with a success message
    public function updateCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $request->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;

            if(File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category updated successfully!');
    }

    // Generates a thumbnail for the category image
    // The thumbnail is resized to 124x124 pixels and saved in the uploads/categories directory
    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());

        $img->cover(124, 124, "top");
        $img->resize(124, 124, function($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath. '/' . $imageName);
    }

    // Deletes a category by its ID
    // Checks if the category exists, deletes the image file from the server if it exists,
    // and then deletes the category from the database
    // Redirects back to the categories page with a success message
    // If the category does not exist, it redirects back with an error message
    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if ($category) {
            if (File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
            $category->delete();
            return redirect()->route('admin.categories')->with('status', 'Category Deleted successfully!');
        }
        return redirect()->route('admin.categories')->with('error', 'Category not found!');
    }

    // Returns the products view in the admin dashboard
    // Retrieves all products from the database, ordered by creation time in descending order
    public function products()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products', compact('products'));
    }

    // Returns the view to add a new product
    // Retrieves all categories and brands from the database, ordered by name
    // Passes the categories and brands to the view for selection in the product form
    public function addProduct()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    // Handles the storage of a new product
    // Validates the request, processes the main image and gallery images, and saves the product to the database
    // Redirects back to the products page with a success message
    // The main image is processed to create a thumbnail and saved in the uploads/products directory
    // The gallery images are processed similarly, resized, and saved in the same directory
    // The product's category and brand are set based on the selected IDs from the form
    // The method also handles the case where the sale price is not provided, allowing it to be nullable
    // The stock status is set to 'in_stock' by default, but can be changed to 'out_of_stock'
    // The method uses the GenerateProductThumbnailsImage method to create a thumbnail for the product image
    // The thumbnail is resized to 540x689 pixels and saved in the uploads/products directory
    // The method also handles the case where the product images are not provided, ensuring that the product can still be created without additional images
    // The gallery images are stored as a comma-separated string in the database
    // The method returns a redirect response to the products page with a success message
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'SKU' => 'required|string|max:100',
            'stock_status' => 'required',
            'featured' => 'nullable|boolean',
            'quantity' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ? true : false;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateProductThumbnailsImage($image, $file_name);
        $product->image = $file_name;

        $gallery_array = array();
        $gallery_images = "";
        $counter = 1;
        if ($request->hasFile('images'))
        {
            $allowedFileExtension = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $files = $request->file('images');
            foreach ($files as $file)
            {
                $gallery_extension = $file->getClientOriginalExtension();
                $gallery_check = in_array($gallery_extension, $allowedFileExtension);
                if ($gallery_check)
                {
                    $gallery_file_name = Carbon::now()->timestamp . '-' . $counter . '.' . $gallery_extension;
                    $this->GenerateProductThumbnailsImage($file, $gallery_file_name);
                    array_push($gallery_array, $gallery_file_name);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_array);
        }
        $product->images = $gallery_images;

        $product->save();
        
        return redirect()->route('admin.products')->with('status', 'Product added successfully!');
    }

    // Generates a thumbnail for the product image
    // The thumbnail is resized to 540x689 pixels and saved in the uploads/products directory
    // Additionally, a smaller thumbnail of 104x104 pixels is created for use in product listings
    // The method uses the Intervention Image library to read, resize, and save the images
    // The main image is saved in the uploads/products directory, while the thumbnail is saved in the uploads/products/thumbnails directory
    // The method ensures that both the main image and thumbnail are created with the same name for consistency
    // The main image is resized to fit within the specified dimensions while maintaining the aspect ratio
    // The thumbnail is also resized to fit within the specified dimensions
    // The method takes the uploaded image and the desired image name as parameters    
    public function GenerateProductThumbnailsImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());

        $img->cover(540, 689, "top");
        $img->resize(540, 689, function($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath. '/' . $imageName);

        $img->cover(104, 104, "top");
        $img->resize(104, 104, function($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail. '/' . $imageName);
    }
}
