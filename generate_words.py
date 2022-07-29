from transformers import pipeline, set_seed,GPT2TokenizerFast, GPT2LMHeadModel

generator = pipeline('text-generation', model='gpt2')
tokenizer = GPT2TokenizerFast.from_pretrained("gpt2")
set_seed(42)
print(generator("Hello, I'm a engineer,", max_length=30, num_return_sequences=5,pad_token_id=tokenizer.eos_token_id))
# Hello, I'm a engineer, 
