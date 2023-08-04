let selected_options = [];
let chosen_input;
chosenInit();
function chosenInit() {
    selected_options = [];
    $(".chosen-select").chosen({
        no_results_text: " "
    });
    
    chosen_input =  document.querySelector('.chosen-search-input');
    if(chosen_input) {
        chosen_input.oninput = function(){loadPopularHashtags(chosen_input)};
    }
}
$(".chosen-select").chosen().change(function(){
    console.log('chos = ', $('.chosen-select').val());
    let current_value = $('.chosen-select').val();
    if(current_value.length > selected_options.length) {
        let all_options_select = document.querySelector('.chosen-select');
        let options = all_options_select.childNodes;
        for(let i = 0;i<options.length;i++) {
            if(options[i].innerText == current_value[0]){
                selected_options.push(options[i]);
                break;
            }
        }
        console.log('save= ', selected_options);
    } else if(current_value.length < selected_options.length) {
        for(let i=0;i<selected_options.length;){
            if(current_value.includes(selected_options[i].innerText)) {
                i++;
            } else {
                selected_options.splice(i, 1);
            }
        }
        console.log('saveD= ', selected_options);
    }
});
function loadPopularHashtags(hashtag_field) {
    let search_hash = hashtag_field.value;
    if (search_hash == '') {
        let hashtags = [];
        document.querySelector('.chosen-select').innerHTML = '';
        hashtags.unshift({name:search_hash});
            hashtags.forEach((hash) => {
                let is_used = false;
                selected_options.forEach((cur) => {
                    if(cur.innerText == hash.name) {
                        is_used = true;
                    }
                });
                if(!is_used) {
                    let option = document.createElement('option');
                    option.innerText = hash.name;
                    document.querySelector('.chosen-select').append(option);
                }
            });
            selected_options.forEach((cur) => {
                document.querySelector('.chosen-select').append(cur);
            });
        $(".chosen-select").trigger("chosen:updated");
        return;
    }
    let form_data = new FormData();
    form_data.append('search', search_hash);
    form_data.append('action', 'popular_hashtags');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", location.origin + "/trade-ajax.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let json = xhr.responseText;
            let hashtags = JSON.parse(json);
            document.querySelector('.chosen-select').innerHTML = '';
            hashtags.unshift({name:search_hash});
            hashtags.forEach((hash) => {
                let is_used = false;
                selected_options.forEach((cur) => {
                    if(cur.innerText == hash.name) {
                        is_used = true;
                    }
                });
                if(!is_used) {
                    let option = document.createElement('option');
                    option.innerText = hash.name;
                    document.querySelector('.chosen-select').append(option);
                }
            });
            selected_options.forEach((cur) => {
                document.querySelector('.chosen-select').append(cur);
            });
            $(".chosen-select").trigger("chosen:updated");
        }
    };
    xhr.send(form_data);
}