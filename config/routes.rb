Rails.application.routes.draw do
  # For details on the DSL available within this file, see https://guides.rubyonrails.org/routing.html
  # get '/', to: 'lounge#main'
  get 'lounge/main'
  get 'lounge/contacts'
  # get '/contacts/', to: 'lounge#contacts'
end
