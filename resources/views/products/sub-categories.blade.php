<select name="child_category" id="" class="form-control child-category">
    <option value="" style="display: none">Sub Categories</option>
    @foreach($sub_categories as $category)
        <option value="{{ $category->title }}" >{{ $category->title }}</option>
    @endforeach
</select>
