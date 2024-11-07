<?php
/*
 exanple formualr contact form 7 with bootstrap
*
 *   CSS customization is needed
*/

// CODE for functions.php
add_filter('wpcf7_autop_or_not', 'wpcf7_autop_return_false');
function wpcf7_autop_return_false() {
    return false;
}

// add_filter('wpcf7_form_elements', 'clear_form_elems' );

function clear_form_elems ($content) {
    $content = preg_replace('/<(span).*?class="\s*(?:.*\s)?wpcf7-form-control-wrap(?:\s[^"]+)?\s*"[^\>]*>(.*)<\/\1>/i', '\2', $content);

    return $content;
}


add_filter('wpcf7_validate_text*', 'custom_text_validation_filter', 20, 2);

function custom_text_validation_filter($result, $tag) {
    
    $tag = new WPCF7_FormTag($tag);
    
    if ('numartelefon' == $tag->name) {
        $field_name = isset( $_POST['numartelefon'] ) ? trim( $_POST['numartelefon'] ) : '';
        
        if (empty($field_name) || !is_numeric($field_name) ) {
             $result->invalidate( $tag, "Numărul de telefon este obligatoriu" );
        }
    }

    return $result;
}

// CODE for contactform field to create the form itself
?>
<div class="input_element_cstm input-group mb-3 " data-name="Nume">
[text nume id:nume class:form-control "Nume"]
                    </div>
                    <div class="input_element_cstm input-group mb-3" data-name="Prenume">
[text prenume id:Prenume class:form-control "Prenume"]
                    </div>
                    <div class="input_element_cstm input-group mb-3">
                        [text* numartelefon id:Numartelefon class:form-control "Număr Telefon"]

                    </div>
                    <div class="input_element_cstm input-group mb-3" data-name="Email">
                        [text emailaddres id:Email class:form-control "Email"]
                    </div>
                    <div class="row">
                        <div class="input_element_cstm mb-3 col col-12 col-md-3"  data-name="Oras">
[select oras class:form-select class:cityselect "Oraș" "oras1" "oras2" "oras3"]
                        </div>
                        <div class="input_element_cstm mb-3 col col-12 col-md-9"  data-name="Pozitie">
[select pozitie class:form-select class:selectiecstm class:oras1 "Poziție" "Muncitor"]
[select pozitie2 class:form-select class:selectiecstm class:oras2 class:hidden "Poziție" "Operator" "Șofer"]

                            
                        </div>
                    </div>
                    <div class="input_element_cstm input-group mb-3 fileuplaod" data-name="upload">
                        <div class="design_upload">
                            <div class="uplaod_desc">
                                <img src="\wp-content\themes\caroli\images\uplaod_icon.png" class="uplaod_icon1">
                                <div class="title_up">Depune CV-ul aici</div>
                                <div class="desc_up"><a href="#">Selectează fișier </a><img src="\wp-content\themes\caroli\images\agraf.png" class="uplaod_icon2"></div>
                            </div>
                            <div class="uploaded_files_details">
                                <div class="filename"></div>
                                div class="desc_up"><a href="#">Selectează fișier </a><img src="\wp-content\themes\caroli\images\agraf.png" class="uplaod_icon2"></div>
                            </div>
                        </div>
[file fileuploadfield filetypes:pdf|doc|docx id:fileuinput class:form-control]
                    </div>
                    <div class="input_element_cstm input-group mb-3 upload_text_subdescription">
                        <div class="simple_text">Se acceptă fișiere de tipul .PDF, .DOC, .DOCX, .JPEG, PNG, HTML. Dimensiune maximă 10MB.</div>
                    </div>
                    <div class="input_element_cstm form_checkbox_cstm_form input-group mb-3 ">
                        <div class="form-check" data-name="gdpr">
           
[acceptance gdpr id:gdpr class:form-check-input]
                          <label class="form-check-label" for="gdpr">
                            Sunt de acord cu prelucrarea datelor personale 
                          </label>
[/acceptance]
                        </div>
                    </div>
                    <div class="input_element_cstm form_checkbox_cstm_form noborder input-group mb-3">
                        <div class="form-check" data-name="gdprform">
[acceptance gdprform id:gdprform class:form-check-input optional]

                          <label class="form-check-label" for="gdprform">
                            Sunt de acord sa fiu de ceva
                          </label>
[/acceptance]
                        </div>
                    </div>
                    <div class="input_element_cstm input-group mb-3">
[submit class:form-control class:btn_red "Aplică acum!"]
                    </div>
