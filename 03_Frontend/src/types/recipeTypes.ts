export interface SingleRecipe {
  id: number;
  title: string;
  slug: string;
  main_image: string;
  partner: number;
  external_url: string;
  ingredients: string;
  nutrition_value: number;
  duration_active: number;
  duration_passive: number;
  categories: string;
  tags: string;
  rating: number;
  vita_score: number;
  water_balance: string;
  co2_emission: number;
  author: number;
  creation_date: string;
  active: number;

  ingredientOverlap?: Array<number>;
}

export interface RecipeFilter {
  ingredientStock: {
    filterInclude: boolean;
    ingredients: Array<number>;
  };
  categories: Array<number>;
  tags: Array<number>;
}