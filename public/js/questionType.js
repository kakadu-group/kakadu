
var courseId;
var choices;
var _csrf;
var url;
var type;

/**
 * Initialises the file with the course id and hides the
 * multiplechoice question type. 
 * @param id: the id of the course
 * @param baseUrl
 */
function initialiseQuestionType(id, baseUrl, questionType){
	this.courseId = id;
	this.choices = 2;
	this.url = baseUrl;
	this.type = questionType;
	console.log("questionType:" + this.type);
	this._csrf =  $('input[name="csrf_token"]').val();	
	if(this.type === "simple"){
		$("#multiple").hide()
		$("#simple").show();
	}else{
		$("#simple").hide();
		$("#multiple").show();
	}
	
}

/**
 * Shows the given question type and hides the other one(s).
 * 
 * @param type: the type of the question
 */
function changeType(type){
	if(type === 'simple'){
		$("#simple").show();
		$('#multiple').hide();
	}
	if(type === 'multiple'){
		$('#multiple').show();
		$("#simple").hide();
	}
}

/**
 * Reads all changed values and edits the question.
 * 
 * @param id: id of the question.
 * @param type: type of the question
 */
function editQuestion(id, type){
	var questionId = id;
	var questionType = type;
	
	console.log(questionId, questionType)
	
	var question = $('#editQuestion'+questionId+' #questionSimple').val();
	var questionJson = JSON.stringify({"question": question});
	console.log(questionJson);
	
	var answer = $('#editQuestion'+questionId+' #answerSimple').val();
	var answerJson = JSON.stringify({"answer": answer});
	console.log(answerJson);
	
	var catalogsSelected = $('#editQuestion'+questionId+' #selectCatalogs').val() || [];
	console.log(catalogsSelected);
}

/**
 * Adds an extra answer choice
 */
function addChoice(){
	var numChoices = $('.choices').length
	
	var extraChoice = '<div id="'+numChoices+'">'+
					  '<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none"></textarea>'+
				 	  ' <input id="check'+numChoices+'" name="right" class="offset1" type="checkbox" value="'+numChoices+'" name="checkbox">'+
				 	  '<button class="btn-danger offset1" onclick="removeChoice('+numChoices+');return false;"><i class="icon-remove"></i></button>'+
				 	  '</div>';
	
	$("#formMultiple #choices").append(extraChoice);
}

/**
 * Removes an extra choice.
 * 
 * @param id: the id of the extra choice which is removed
 */
function removeChoice(id){
	if($("#check"+id).is(':checked')){
		console.log("dsfadsafsad");
		console.log(id);
		$('input[type="hidden"][value="'+id+'"][name="answer[]"]').remove();
	} 
	$("#"+id).remove();
}

/**
 * Is fired on a click on a radio button. It reads the value of the radio button
 * and writes it in the hidden question field.
 */
$(document).ready(function(){
	$('#choices').on('change', 'input:checkbox', function () {
		if($(this).attr('checked')){	
			var answer = '<input type="hidden" name="answer[]" value='+$(this).val()+'>';
			$("#formMultiple #choices").append(answer);
		}else{
			$('input[type="hidden"][value="'+$(this).val()+'"]').remove();
		}
	});
});



